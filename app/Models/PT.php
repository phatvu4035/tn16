<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PT extends BaseModel
{
    protected $table = 'pt';


    protected $fillable = ['id','parent_id','level','name_native','short_name','name_en','complete_code','short_code','tax_code','location','address_in_country','address_in_english','status','active','created_at','updated_at'];


}