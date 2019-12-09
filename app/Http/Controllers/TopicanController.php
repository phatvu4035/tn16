<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Http\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Repositories\Eloquents\UserEloquent;

use App\Http\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Repositories\Eloquents\RoleEloquent;

use Illuminate\Http\Request;


class TopicanController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        Topica::canOrRedirect('index.user');
        $getData = $request->all();
        
        return view('topican.index', compact('getData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Topica::canOrRedirect('add.user');
        $roles = $this->roleRepository->getDataBy();

        return view('topican.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Topica::canOrRedirect('add.user');
        $this->userValidate($request);
        $emailValidate = $this->userEmailValidate($request);

        if (!$emailValidate) {
            $request->flash();
            return redirect()->route('topican.create')->with('error', $request->email . ' không hợp lệ');
        } else {
            // Pass all validate
            $this->userRepository->createOrUpdateUser($request->all());
            return redirect()->route('topican.index')->with('message',[
                    "content" =>  $request->name . ' đã được tạo !',
                    'title' => "Thành công",
                    'type' => 'primary'
                ]
            );
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        Topica::canOrRedirect('edit.user');
        $data = $this->userRepository->getDataBy(['id' => $id])->first();

        $roles = $this->roleRepository->getDataBy();

        return view('topican.edit', compact('data', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        Topica::canOrRedirect('edit.user');
        $this->userValidate($request);
        $emailValidate = $this->userEmailValidate($request);

        if (!$emailValidate) {
            $request->flash();
            return redirect()->route('topican.edit', $id)->with('error', $request->email . ' không hợp lệ !');
        } else {
            // Pass all validate
            $this->userRepository->createOrUpdateUser($request->all());
            return redirect()->route('topican.index')->with('message',
                [
                    "content"=>$request->name . ' đã được cập nhật !',
                    'title'=>"Thành công",
                    'type'=>"primary"
                ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Topica::canOrRedirect('delete.user');
        $message = $this->userRepository->destroy($id);

        return redirect()->route('topican.index')->with("message", [
            "title" => "Khóa nhân viên thành công",
            "content" => $message['message'],
            "type" => $message['type']
        ])->send();
    }

    public function active($id)
    {
        Topica::canOrRedirect('delete.user');
        $user = [
            'id' => $id,
            'active' => 1
        ];
        $message = $this->userRepository->createOrUpdateUser($user);

        return redirect()->route('topican.index')->with("message", [
            "title" => "Thành công",
            "content" => "Mở khóa nhân viên thành công",
            "type" => "success"
        ])->send();
    }

    public function userValidate($request)
    {
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email,' . $request->id,
            'role_id' => 'numeric',
        ),
            array(
                'name.required' => 'Vui lòng điền tên tài khoản !',
                'name.max:255' => 'Tên không được vượt quá 255 kí tự.',
                'email.required' => 'Vui lòng điền email !',
                'email.unique' => 'Email đã được sử dụng.',
                'role_id.numeric' => 'Vui lòng chọn một quyền !'
            )
        );
    }

    /**
     * @param $action must be 'update' or 'add'
     */
    public function userEmailValidate($request)
    {
        $email = $request->email;
        if (!validateEmailTopica($email)) {
            return false;
        }
        return true;

    }

}
