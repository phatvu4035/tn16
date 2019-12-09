<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends BaseModel
{
    protected $table = 'summary';


    protected $fillable = ['data','sum_thu_nhap_truoc_thue','sum_non_tax','sum_tnct','sum_bhxh','sum_thue_tam_trich','sum_thuc_nhan','month', 'year', 'employee_code', 'employee_table', 'phap_nhan', 'san_pham', 'ma_so_thue', 'tong_thu_nhap_truoc_thue', 'tong_non_tax', 'tong_tnct', 'bhxh', 'thue_tam_trich', 'thuc_nhan', 'giam_tru_ban_than', 'giam_tru_gia_canh', 'type', 'note', 'ref', 'noi_dung', 'status', 'vi_tri', 'cdt', 'order_id', 'ngay_thanh_toan', '', 'da_thanh_toan', 'con_lai_can_thanh_toan', 'thue_da_trich'];

    public function typeName()
    {
        return $this->belongsTo(Type::class, 'type', 'id');
    }

    public function order()
    {
        return $this->belongsTo(OrderInfo::class, 'order_id', 'id');
    }

    public function employees()
    {
        return $this->hasOne(Employee::class, 'employee_code', 'employee_code');
    }

    public function employeeRent()
    {
        return $this->hasOne(EmployeeRent::class, 'identity_code', 'employee_code');
    }
    public function employeeRentWithDelete()
    {
        return $this->hasOne(EmployeeRent::class, 'identity_code', 'employee_code')->withTrashed();
    }

    public function getRelationAttribute()
    {
        if ($this->employee_table == 'employees') {
            return 'employees';
        }
        if ($this->employee_table == 'employee_rent') {
            return 'employeeRent';
        }
        return '';
    }

}
