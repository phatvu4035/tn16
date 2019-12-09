<?php

namespace App\Models;

use App\Http\Repositories\Eloquents\EmployeeOrderEloquent;
use Illuminate\Database\Eloquent\Model;

class OrderInfo extends BaseModel
{
    const CROSS_CHECK_UNDONE = 0;
    const CROSS_CHECK_DONE = 1;

    protected $table = 'order_info';

    protected $fillable = ['ma_osscar', 'san_pham', 'ma_du_toan', 'serial', 'phong_ban', 'phap_nhan', 'ngay_de_xuat', 'nguoi_de_xuat', 'noi_dung', 'nguoi_huong', 'so_tien', 'loai_tien', 'ty_gia', 'quy_doi', 'month', 'year', 'status', 'ngay_thanh_toan','isSalary', 'dot_thanh_toan', 'additional_order', 'reference_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeOrder()
    {
        return $this->hasMany(EmployeeOrder::class, 'order_id');
    }

    public function summary()
    {
        return $this->hasMany(Summary::class, 'order_id', 'id');
    }
}
