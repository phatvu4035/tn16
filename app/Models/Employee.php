<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends BaseModel
{

    protected $table = 'employees';

    protected $primaryKey = 'employee_code';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_code', 'last_name', 'first_name','email', 'cmt', 'phap_nhan','birthday','bank','bank_account','status_hr20','type', 'mst', 'vi_tri', 'api_updated_time'
    ];

    public function getFullNameAttribute() {
        return ucfirst($this->last_name) . ' ' . ucfirst($this->first_name);
    }

}
