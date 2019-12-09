<?php
/**
 * Created by PhpStorm.
 * User: johnavu
 * Date: 25/10/2017
 * Time: 10:05
 */

namespace App\Traits;

trait TopicaUsers
{

    public function hasPermission($name)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }


        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }
        
        return in_array($name, $this->role->permissions->pluck('slug')->toArray());
    }

    public function hasPermissionOrFail($name)
    {
        if (!$this->hasPermission($name)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function hasPermissionOrAbort($name, $statusCode = 403)
    {
        if (!$this->hasPermission($name)) {
            return abort($statusCode);
        }

        return true;
    }

    public function getStatus() {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        if (!$this->role->relationLoaded('status')) {
            $this->role->load('status');
        }

        return $this->role->status->pluck('name','code')->toArray(); 
    }

    public function hasStatus($status_code)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        if (!$this->role->relationLoaded('status')) {
            $this->role->load('status');
        }

        return in_array($status_code, $this->role->status->pluck('code')->toArray());
    }
}