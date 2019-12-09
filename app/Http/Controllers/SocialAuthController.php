<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use App\Http\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Socialite, Auth, Redirect, Session, URL;

use App\Http\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Models\Role;
use App\Models\Employee;


class SocialAuthController extends Controller
{
    protected $userRepository;

    protected $roleRepositoryInterface;

    protected $employeeRepositoryInterface;

    protected $permissionRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepositoryInterface, EmployeeRepositoryInterface $employeeRepositoryInterface, PermissionRepositoryInterface $permissionRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepositoryInterface = $roleRepositoryInterface;
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Chuyển hướng người dùng sang OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider = 'google')
    {
        if (!Session::has('pre_url')) {
            Session::put('pre_url', URL::previous());
        } else {
            if (URL::previous() != URL::to('login')) Session::put('pre_url', URL::previous());
        }
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Lấy thông tin từ Provider, kiểm tra nếu người dùng đã tồn tại trong CSDL
     * thì đăng nhập, ngược lại nếu chưa thì tạo người dùng mới trong SCDL.
     *
     * @return Response
     */
    public function handleProviderCallback($provider = 'google')
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
            $authUser = $this->findOrCreateUser($user, $provider);

            Auth::login($authUser);

            return Redirect::to(Session::get('pre_url'));
        } catch (\Exception $e) {
            Log::info("Lỗi đăng nhập : " . $e->getTraceAsString());
            return redirect('/');
        }

    }

    /**
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        if (!validateEmailTopica($user->email)) {
            return redirect('/login')->send();
        }

        $authUser = $this->userRepository->getByEmail($user->email);
        $role = $this->checkOrCreateDefaultRole();
//        d($authUser->active);

        if (is_null($authUser)) {
            $authUser = $this->userRepository->createOrUpdateUser([
                'id' => 0,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'abc',
                'provider' => $provider,
                'provider_id' => $user->id,
                'role_id' => $role->id,
                'avatar' => $user->avatar,
                'active' => 1
            ]);
        }
        if ($authUser->active == 0) {
            return redirect('/login')->with("message", [
                "title" => "Thất bại",
                "content" => "Tài khoản của bạn đã bị khóa, vui lòng liên hệ admin để biết thêm chi tiết",
                "type" => "danger"
            ])->send();
        }
        $employees = $this->getEmployeeByEmail($user->email);

        $employee_code = '';
        if (count($employees)) {
            $employee = $employees[0];
            $employee_code = $employee->employee_code;
        }

        $authUser->employee_code = $employee_code;

        $authUser->save();

        return $authUser;

    }

    public function checkOrCreateDefaultRole()
    {
        $role = $this->roleRepositoryInterface->getDataBy(['name' => 'Topican'])->first();

        $data = array(
            'id' => 0,
            'name' => 'Topican',
            'description' => 'Vai trò mặc định',
            'permissions' => [],
        );

        if (is_null($role)) {
            $role = $this->roleRepositoryInterface->createOrUpdateData($data);
        }

        // create deafault admin
        $roleAdmin = $this->roleRepositoryInterface->getDataBy(['name' => 'Administrator'])->first();
//        dd($roleAdmin);
        if (is_null($roleAdmin)) {
            app(PermissionController::class)->createPermission(false);
            $p = $this->permissionRepository->getDataBy([], false)->pluck('id');
            $this->roleRepositoryInterface->createOrUpdateData([
                'id' => 0,
                'name' => 'Administrator',
                'description' => 'Administrator',
                'permissions' => $p,
                'isCreate' => true
            ]);
        }

        return $role;
    }

    public function getEmployeeByEmail($email)
    {
        $employees = $this->employeeRepositoryInterface->getDataBy(['email' => $email]);

        return $employees;
    }


}