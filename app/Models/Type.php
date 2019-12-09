<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends BaseModel
{
    protected $table = 'type';


    protected $fillable = ['id','name', 'title'];

    /**
     * Nhận các cái summary cho từng type.
     */
    public function summarys()
    {
        return $this->hasMany(Summary::class, 'type', 'id');
    }


}
