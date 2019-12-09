<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Models\Permission;
use App\Models\Summary;
use App\Models\Type;
use Illuminate\Pagination\Paginator;


class TypeEloquent extends BaseEloquent implements TypeRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Type::class;
    }

    /**
     * @param array $conditions
     * @param bool $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true)
    {
        $data = $this->model;

        if ($conditions) {

            if (isset($conditions['search']) && $conditions['search']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('name', 'like', '%' . $conditions['search'] . '%');
                });
            }

            if (isset($conditions['id']) && $conditions['id']) {
                $data = $data->where('id', $conditions['id']);
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
            return $data->paginate(ITEM_NUMBER);
        } else {
            return $data->get();
        }

    }


    public function saveData($data)
    {
        foreach ($data as $d) {
            if (isset($d['id']) && $d['id']) {
                $model = Type::find($d['id']);
            } else {
                $model = new Type();
            }
            if (!$model) $model = new Type();
            $model->fill($d)->save();
        }
        return true;
    }

    // Check xem da có bản ghi nào lớn hơn 1000 chưa
    public function checkTypeFrom1000()
    {
        $data = $this->model;
        $data = $data->where('id', '>', 1000)->get();
        if(count($data) > 0) {
            return true;
        } else {
            return false;
        }
    }

}


?>