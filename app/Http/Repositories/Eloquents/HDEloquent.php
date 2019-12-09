<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\HDRepositoryInterface;
use App\Models\HD;
use Illuminate\Pagination\Paginator;


class HDEloquent extends BaseEloquent implements HDRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return HD::class;
    }

    /**
     * @param array $conditions
     * @param bool $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = false)
    {
        $data = $this->model;

        if ($conditions) {

            if (isset($conditions['search']) && $conditions['search']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('short_code', 'like', '%' . $conditions['search'] . '%');
                });
            }

            // get max date
            if (isset($conditions['maxDate']) && $conditions['maxDate']) {
                return $data->orderBy('updated_at', 'desc')->first();
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

    public function saveMany($data)
    {
        $model = $this->model;
        foreach ($data as $key => $value) {
            if (isset($value['id'])) {
                $char = $model::firstOrNew(['id' => $value['id']]);
                $char->fill($value);
                $char->save();
            }
        }
    }

}


?>