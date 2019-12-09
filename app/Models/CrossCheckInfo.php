<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossCheckInfo extends BaseModel
{
    protected $table = 'cross_check_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'phap_nhan', 'thang', 'nam', 'ke_toan_check', 'created_at', 'updated_at', 'is_salary', 'ke_toan_id'];

    public function crossCheck() {
        return $this->hasMany(CrossCheck::class, "info_id", "id");
    }
}
