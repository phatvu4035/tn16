<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
    //
    protected $table = 'roles';

    protected $fillable = ['name', 'description'];


    /**
	* Relationship to permission model
    */
    public function permissions()
    {
    	return $this->belongsToMany('App\Models\Permission', 'role_permission', 'role_id', 'permission_id');
    }

    public function permissionPN()
    {
        return $this->belongsToMany(PT::class, 'role_phap_nhan', 'role_id', 'phap_nhan_id');
    }
}
