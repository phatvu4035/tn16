<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossCheck extends BaseModel
{

    protected $table = 'cross_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'serial',
        'info_id',
        'ngay_chung_tu',
        'ma_chung_tu',
        'so_chung_tu',
        'ma_khach',
        'ten_khach',
        'dien_giai',
        'tai_khoan',
        'tai_khoan_doi_ung',
        'ps_no',
        'ps_co',
        'ma_du_an',
        'ma_chung_tu_0',
        'status',
        'order_id',
        'thue',
        'temp_order',
        'active',
        'reason',
        'phap_nhan',
        'tcb_id'
    ];

    public function order()
    {
        return $this->hasOne(OrderInfo::class, 'id', 'order_id');
    }

    public function tax()
    {
//        return $this->hasMany(EmployeeOrder::class, 'order_id', 'order_id')->selectRaw("employees_order.order_id, sum(tncn) as sumTax")->groupBy("employees_order.order_id");
        return $this->hasMany(Summary::class, 'order_id', 'order_id')->selectRaw("summary.order_id, sum(thue_tam_trich) as sumTax")->groupBy("summary.order_id");
    }

    public function tnct()
    {
        return $this->hasMany(Summary::class, 'order_id', 'order_id')->selectRaw("summary.order_id, sum(tong_tnct) as sumTnct")->groupBy("summary.order_id");
    }
}
