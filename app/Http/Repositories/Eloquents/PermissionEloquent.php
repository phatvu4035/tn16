<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use App\Models\Permission;

class PermissionEloquent extends BaseEloquent implements PermissionRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Permission::class;
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
            if (isset($conditions['slug']) && $conditions['slug']) {
                if (is_array($conditions['slug'])) {
                    $data = $data->whereIn('slug', $conditions['slug']);
                } else {
                    $data = $data->where('slug', $conditions['slug']);
                }
            }

            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {

                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
        }

        if ($pagination) {
            return $data->orderBy('slug')->paginate(ITEM_NUMBER);
        } else {
            return $data->orderBy('slug')->get();
        }

    }


    public function saveData($data)
    {
        foreach ($data as $d) {
            if (isset($d['id']) && $d['id']) {
                $model = Permission::find($d['id']);
            } else {
                $model = new Permission();
            }
            if (!$model) $model = new Permission();
            $model->fill($d)->save();
        }
        return true;
    }

}


?>