<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function createPermission($isUrl = true)
    {
        if ($isUrl)
            if (!in_array(Auth::user()->email, getListEmailTopica())) {
                return redirect('/');
            }
        $arr = [
            [
                'id' => 1,
                'name' => 'Add User',
                'slug' => 'add.user',
                'description' => 'Thêm mới thành viên',
                'group_name' => 'Thành viên',
                'group_slug' => 'users'
            ],
            [
                'id' => 2,
                'name' => 'Edit User',
                'slug' => 'edit.user',
                'description' => 'Sửa thành viên',
                'group_name' => 'Thành viên',
                'group_slug' => 'users'
            ],
            [
                'id' => 3,
                'name' => 'Delete User',
                'slug' => 'delete.user',
                'description' => 'Xóa thành viên',
                'group_name' => 'Thành viên',
                'group_slug' => 'users'
            ],
            [
                'id' => 4,
                'name' => 'View User',
                'slug' => 'index.user',
                'description' => 'Danh sách thành viên',
                'group_name' => 'Thành viên',
                'group_slug' => 'users'
            ],
            [
                'id' => 5,
                'name' => 'Add Role',
                'slug' => 'add.role',
                'description' => 'Thêm quyền',
                'group_name' => 'Quyền',
                'group_slug' => 'roles'
            ],
            [
                'id' => 6,
                'name' => 'Edit Role',
                'slug' => 'edit.role',
                'description' => 'Sửa quyền',
                'group_name' => 'Quyền',
                'group_slug' => 'roles'
            ],
            [
                'id' => 7,
                'name' => 'Delete Role',
                'slug' => 'delete.role',
                'description' => 'Xóa quyền',
                'group_name' => 'Quyền',
                'group_slug' => 'roles'
            ],
            [
                'id' => 8,
                'name' => 'View Role',
                'slug' => 'index.role',
                'description' => 'Danh sách quyền',
                'group_name' => 'Quyền',
                'group_slug' => 'roles'
            ],
            [
                'id' => 9,
                'name' => 'Add order',
                'slug' => 'add.order',
                'description' => 'Thêm bộ thanh toán',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 10,
                'name' => 'Edit order',
                'slug' => 'edit.order',
                'description' => 'Sửa bộ thanh toán (Tất cả)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 28,
                'name' => 'Edit order self',
                'slug' => 'edit.order.self',
                'description' => 'Sửa bộ thanh toán (của người tạo)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 11,
                'name' => 'Delete order',
                'slug' => 'delete.order',
                'description' => 'Xóa bộ thanh toán (Tất cả)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 29,
                'name' => 'Delete order self',
                'slug' => 'delete.order.self',
                'description' => 'Xóa bộ thanh toán (của người tạo)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 12,
                'name' => 'View order',
                'slug' => 'index.order',
                'description' => 'Danh sách bộ thanh toán (Tất cả)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 30,
                'name' => 'View order self',
                'slug' => 'index.order.self',
                'description' => 'Danh sách bộ thanh toán (của người tạo)',
                'group_name' => 'Bộ thanh toán',
                'group_slug' => 'orders'
            ],
            [
                'id' => 13,
                'name' => 'Add Rent Employee',
                'slug' => 'add.rent_employee',
                'description' => 'Thêm nhân sự thuê khoán',
                'group_name' => 'Nhân sự thuê khoán',
                'group_slug' => 'rent_employee'
            ],
            [
                'id' => 14,
                'name' => 'Edit Rent Employee',
                'slug' => 'edit.rent_employee',
                'description' => 'Sửa nhân sự thuê khoán',
                'group_name' => 'Nhân sự thuê khoán',
                'group_slug' => 'rent_employee'
            ],
            [
                'id' => 15,
                'name' => 'Delete Rent Employee',
                'slug' => 'delete.rent_employee',
                'description' => 'Xóa nhân sự thuê khoán',
                'group_name' => 'Nhân sự thuê khoán',
                'group_slug' => 'rent_employee'
            ],
            [
                'id' => 16,
                'name' => 'View Rent Employee',
                'slug' => 'index.rent_employee',
                'description' => 'Danh sách nhân sự thuê khoán',
                'group_name' => 'Nhân sự thuê khoán',
                'group_slug' => 'rent_employee'
            ],
            [
                'id' => 17,
                'name' => 'Export 401',
                'slug' => 'export.401',
                'description' => 'Export 401',
                'group_name' => 'Export',
                'group_slug' => 'export'
            ],
            [
                'id' => 18,
                'name' => 'Export 402',
                'slug' => 'export.402',
                'description' => 'Export 402',
                'group_name' => 'Export',
                'group_slug' => 'export'
            ],
            [
                'id' => 19,
                'name' => 'Export 403',
                'slug' => 'export.403',
                'description' => 'Export 403',
                'group_name' => 'Export',
                'group_slug' => 'export'
            ],
            [
                'id' => 20,
                'name' => 'View TN',
                'slug' => 'index.tn',
                'description' => 'Danh sách thu nhập',
                'group_name' => 'Tra cứu',
                'group_slug' => 'tn'
            ],
            [
                'id' => 22,
                'name' => 'View Cross Check',
                'slug' => 'index.cross_check_status',
                'description' => 'Xem danh sách trạng thái đối soát của các tháng',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 23,
                'name' => 'View Result Cross Check',
                'slug' => 'index.cross_check_result',
                'description' => 'Xem kết quả đối soát',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 24,
                'name' => 'View Cross Check',
                'slug' => 'index.cross_check',
                'description' => 'Thực hiện đối soát',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 25,
                'name' => 'Delete Cross Check',
                'slug' => 'delete.cross_check',
                'description' => 'Hủy đối soát',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 26,
                'name' => 'Export Cross Check',
                'slug' => 'export.cross_check',
                'description' => 'Export đối soát',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 27,
                'name' => 'Xác nhận đối soát của kế toán',
                'slug' => 'export.cross_check_kt_check',
                'description' => 'Xác nhận đối soát của kế toán',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
            [
                'id' => 28,
                'name' => 'Remove Cross Check',
                'slug' => 'remove.cross_check',
                'description' => 'Tắt đối soát',
                'group_name' => 'Đối soát',
                'group_slug' => 'cross_check'
            ],
        ];
        $this->permissionRepository->saveData($arr);
        if ($isUrl)
            return redirect()->route('roles.index');
    }
}
