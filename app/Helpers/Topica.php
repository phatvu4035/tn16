<?php
/**
 * Created by PhpStorm.
 * User: johna
 * Date: 8/7/2018
 * Time: 2:29 PM
 */

namespace App\Helpers;


use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Topica
{
    protected $permissionsLoaded = false;
    protected $permissions = [];

    public function can($permission, $model = null)
    {
        $this->loadPermissions();
        $user = $this->getUser();
        if($user->role->name=='Administrator'){
            return true;
        }
        if($user->role->name=='Topican'){
            return false;
        }
        // Check if permission exist
        $exist = $this->permissions->where('slug', $permission)->first();
        if ($exist) {

            // If input permission not in array permissions
            if (!in_array($permission, $this->permissions->pluck('slug')->toArray()))
                return false;

            if (in_array(Auth::user()->email, getListEmailTopica())) {
                if ($permission == 'index.role' || $permission == 'add.role' || $permission == 'edit.role' || $permission == 'delete.role') {
                    return true;
                }
                if ($permission == 'index.user' || $permission == 'add.user' || $permission == 'edit.user' || $permission == 'delete.user') {
                    return true;
                }
            }

            if ($user == null || !$user->hasPermission($permission)) {
                $permission_self = $permission . '.self';
                if (in_array($permission_self, $this->permissions->pluck('slug')->toArray())) {
                    if ($user->hasPermission($permission_self)) {
                        if (isset($model->created_by) && $model->created_by == Auth::user()->id) {
                            return $permission_self;
                        }
                        if(substr($permission,0,5)=='index'){
                            return $permission_self;
                        }
                    }
                }
                return false;
            }

            return true;
        }

        return false;

    }

    public function canOrFail($permission, $model = null)
    {
        if (!$this->can($permission, $model)) {
            throw new UnauthorizedException(null);
        }

        return true;
    }

    public function canOrAbort($permission, $model = null, $statusCode = 403)
    {
        if (!$this->can($permission, $model)) {
            return abort($statusCode);
        }

        return true;
    }

    public function canOrRedirect($permission, $model = null)
    {
//        dd($this->can($permission, $model));
        if (!$this->can($permission, $model)) {
            return redirect('/')->send();
        } 

        return true;
    }

    public function canCrossOrAbort($permission, $phap_nhan) {
        $user = $this->getUser();
        if($user->role->name=='Administrator'){
            return true;
        }

        if (!$this->canCross($permission, $phap_nhan)) {
            return abort(403);
        }

        return true;
    }

    public function canCross($permission, $phap_nhan) {
        $user = $this->getUser();
        if($user->role->name=='Administrator'){
            return true;
        }

        $user = $this->getUser();
        $allowPN = array_column($user->role->permissionPN->toArray(), "short_code");
        if (!in_array($phap_nhan, $allowPN)) {
            return false;
        }

        return $this->can($permission, null);
    }

    protected function loadPermissions()
    {
        if (!$this->permissionsLoaded) {
            $this->permissionsLoaded = true;
            $this->permissions = Permission::all();
        }
    }

    protected function getUser($id = null)
    {
        if (is_null($id)) {
            $id = auth()->check() ? auth()->user()->id : null;
        }

        if (is_null($id)) {
            return;
        }

        if (!isset($this->users[$id])) {
            $this->users[$id] = User::find($id);
        }

        return $this->users[$id];
    }

}