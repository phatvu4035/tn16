<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    //
    protected $fillable = ['id','name','slug','description','group_slug','group_name'];

    /**
	* Relationship to roles model
    */
    public function roles()
    {
    	return $this->belongsToMany('App\Models\Role', 'role_permission', 'permission_id', 'role_id');
    }

}
