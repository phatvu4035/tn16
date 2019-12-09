<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmployeeRent extends BaseModel
{

    use SoftDeletes;

    protected $table = 'employee_rent';


    protected $fillable = ['identity_code','identity_type','emp_code_date','emp_code_place','emp_name','emp_tax_code','emp_country','emp_live_status','emp_account_number','emp_account_bank'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
