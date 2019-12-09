<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SP extends BaseModel
{
    protected $table = 'sp';


    protected $fillable = ['parent_id','level','product_name_vn','product_name_en','complete_code','shortened_code','payment_outside','payment_inside','status','active','created_at','updated_at'];


}