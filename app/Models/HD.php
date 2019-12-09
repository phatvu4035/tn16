<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HD extends BaseModel
{
    protected $table = 'hd';


    protected $fillable = ['id','parent_id','level','activity_code','body','complete_code','shortened_code','formula','define','tot','toa','cf','status','active','created_at','updated_at'];


}
