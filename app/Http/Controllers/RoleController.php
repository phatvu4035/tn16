<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Http\Repositories\Contracts\PTRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Repositories\Contracts\PermissionRepositoryInterface;


class RoleController extends Controller
{
    protected $roleRepository;

    protected $permissionRepository;

    protected $PTRepository;

    public function __construct(RoleRepositoryInterface $roleRepository, PTRepositoryInterface $PTRepository, PermissionRepositoryInterface $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->PTRepository = $PTRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Topica::canOrRedirect('index.role');
        $data = $this->roleRepository->getDataBy([]);

        return view('roles.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Topica::canOrRedirect('add.role');
        $permissions = $this->permissionRepository->getDataBy();
        $list_permission = [];
        foreach ($permissions as $p) {
            $list_permission[$p['group_slug']]['group_slug'] = $p['group_slug'];
            $list_permission[$p['group_slug']]['group_name'] = $p['group_name'];
            $list_permission[$p['group_slug']]['list'][] = $p;
        }
        $phap_nhan = $this->PTRepository->getDataBy([],false)->pluck('short_code','id');
        return view('roles.add', compact(['list_permission', 'phap_nhan']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Topica::canOrRedirect('add.role');
        $this->roleValidate($request);
//        dd($request->all());
        $role = $this->roleRepository->createOrUpdateData($request->all());
        return redirect()->route('roles.index')->with('message', [
            'title' => 'Thành công',
            'content' => 'Quyền ' . $request->name . ' đã được tạo !',
            'type' => 'primary'
        ]);
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
        Topica::canOrRedirect('edit.role');
        $data = $this->roleRepository->getDataBy(['id' => $id])->first();

        $permissions = $this->permissionRepository->getDataBy()->toArray();

        $list_permission = [];
        foreach ($permissions as $p) {
            $list_permission[$p['group_slug']]['group_slug'] = $p['group_slug'];
            $list_permission[$p['group_slug']]['group_name'] = $p['group_name'];
            $list_permission[$p['group_slug']]['list'][] = $p;
        }
        $phap_nhan = $this->PTRepository->getDataBy([],false)->pluck('short_code','id');
        return view('roles.add', compact(['data', 'list_permission','phap_nhan']));

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
        Topica::canOrRedirect('edit.role');
        $this->roleValidate($request);
        $this->roleRepository->createOrUpdateData($request->all());
        return redirect()->route('roles.index')->with('message', [
                'content' => 'Quyền ' . $request->name . ' đã được cập nhật !',
                'title' => "Thành công!",
                'type' => "primary"
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Topica::canOrRedirect('delete.role');
        $message = $this->roleRepository->destroy($id);

        if ($message['type'] == 'message') {
            return redirect()->route('roles.index')->with($message['type'], ["content" => $message['message'], 'title' => "Thành công", 'type' => "primary"]);
        }
        return redirect()->route('roles.index')->with($message['type'], $message['message']);
    }

    public function roleValidate($request)
    {
        $this->validate($request, array(
            'name' => 'required|string|max:255|unique:roles,name,' . $request->id,
            'description' => 'required',
        ),
            array(
                'name.required' => 'Vui lòng điền tên quyền',
                'name.unique' => 'Tên quyền đã được sử dụng',
                'description.required' => 'Vui lòng thêm phần mô tả'
            )
        );
    }

}