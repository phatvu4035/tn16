<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CDT extends BaseModel
{
    protected $table = 'cdt';


    protected $fillable = ['id', 'parent_id', 'level', 'division_name_vn', 'division_name_en', 'complete_code', 'shortened_code', 'proposal', 'approved', 'proposal_name', 'approved_name', 'user_create_id', 'user_create_name', 'decision_number', 'upgrade_note', 'status', 'active', 'created_at', 'updated_at'];


}