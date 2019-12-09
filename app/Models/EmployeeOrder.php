<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeOrder extends BaseModel
{
    protected $table = 'employees_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_code', 'vi_tri', 'phap_nhan', 'san_pham', 'status_hr20', 'order_id', 'salary_base', 'salary_action', 'salary_working_day', 'salary_working_ot_time',
        'salary_sub', 'salary_other', 'com', 'bonus', 'rent', 'interest', 'bao_hiem', 'giam_tru_ban_than', 'total_salary_sub_other', 'giam_tru_nguoi_phu_thuoc', 'thu_nhap_khong_chiu_thue',
        'tong_giam_tru_tinh_thue', 'thu_nhap_tinh_thue', 'tncn', 'thuc_nhan', 'cdt', 'employee_table', 'note', 'noi_dung'
    ];

    public function employees()
    {
        return $this->hasOne(Employee::class, 'employee_code', 'employee_code');
    }

    public function employeeRent()
    {
        return $this->hasOne(EmployeeRent::class, 'identity_code', 'employee_code');

    }
}
