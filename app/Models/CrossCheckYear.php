<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossCheckYear extends BaseModel
{

    const NONE_EXIST_IN_YEAR = 1;

    protected $table = 'cross_check_year';

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
        'active',
        'reason',
        'phap_nhan',
        'tcb_id',
        'month_order_id'
    ];

    public function order()
    {
        return $this->hasOne(OrderInfo::class, 'id', 'order_id');
    }

    public function monthOrder()
    {
        return $this->hasOne(OrderInfo::class, 'id', 'month_order_id');
    }

    public function monthTax()
    {
        return $this->hasMany(Summary::class, 'order_id', 'month_order_id')->selectRaw("summary.order_id, sum(thue_tam_trich) as sumTax")->groupBy("summary.order_id");
    }

    public function tax()
    {
//        return $this->hasMany(EmployeeOrder::class, 'order_id', 'order_id')->selectRaw("employees_order.order_id, sum(tncn) as sumTax")->groupBy("employees_order.order_id");
        return $this->hasMany(Summary::class, 'order_id', 'order_id')->selectRaw("summary.order_id, sum(thue_tam_trich) as sumTax")->groupBy("summary.order_id");
    }
}
