<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\RoleRepositoryInterface;
use App\Models\Role;

class RoleEloquent extends BaseEloquent implements RoleRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Role::class;
    }

    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = false)
    {
        $data = $this->model;

        if ($conditions) {
            /**
             * Find by name
             */
            if (isset($conditions['id']) && $conditions['id']) {
                $data = $data->where('id', $conditions['id']);
            }
            if (isset($conditions['name']) && $conditions['name']) {
                $data = $data->where('name', $conditions['name']);
            }
        }

        if ($pagination) {
            return $data->paginate(ITEM_NUMBER);
        } else {
            return $data->get();
        }
    }

    public function createOrUpdateData(array $data)
    {

        $role = $this->model->firstOrNew(['id' => $data['id']])->fill($data);

        if (isset($role->name) && ($role->name == 'Administrator' || $role->name == 'Topican')) {
            if (isset($data['isCreate']) && $data['isCreate']) {

            } else {
                return false;
            }
        }
        $role->save();
//        dd($data);
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($permissions['phap_nhan']);
            $role->permissions()->sync($permissions);
        } else {
            $role->permissions()->sync([]);
        }
        if (isset($data['permissions']['phap_nhan'])) {
            $role->permissionPN()->sync($data['permissions']['phap_nhan']);
        } else {
            $role->permissionPN()->sync([]);
        }
        return $role;
    }

    public function destroy($id)
    {
        $message = [];
        $role = $this->getDataBy(['id' => $id])->first();

        if ($role->name == 'Topican') {
            $message['type'] = 'error';
            $message['message'] = 'Quyền Topican không thể xóa';
            return $message;
        }
        if ($role->name == 'Administrator') {
            $message['type'] = 'error';
            $message['message'] = 'Quyền Administrator không thể xóa';
            return $message;
        }
        $role->permissions()->sync([]);
        $role->permissionPT()->sync([]);
        /*
        * Delete role
        */
        $role->delete();

        $message['type'] = 'message';
        $message['message'] = 'Đã xóa quyền ' . $role->name;

        return $message;
    }

}

?>