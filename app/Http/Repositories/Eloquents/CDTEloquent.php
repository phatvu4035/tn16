<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\CDTRepositoryInterface;
use App\Models\CDT;
use Illuminate\Pagination\Paginator;


class CDTEloquent extends BaseEloquent implements CDTRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return CDT::class;
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
                    $q->where('complete_code', 'like', '%' . $conditions['search'] . '%');
                });
            }

            // get max date
            if (isset($conditions['maxDate']) && $conditions['maxDate']) {
                return $data->orderBy('updated_at', 'desc')->first();
            }

            // get by shortened_code
            if (isset($conditions['shortened_code']) && $conditions['shortened_code']) {
                $data = $data->where('shortened_code', $conditions['shortened_code']);
            }

            // get by shortened_code
            if (isset($conditions['complete_code']) && $conditions['complete_code']) {
                $data = $data->where('complete_code', $conditions['complete_code']);
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