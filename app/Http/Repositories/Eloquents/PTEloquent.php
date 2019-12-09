<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\PTRepositoryInterface;
use App\Models\PT;
use Illuminate\Pagination\Paginator;


class PTEloquent extends BaseEloquent implements PTRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return PT::class;
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

            // get by short_code
            if (isset($conditions['short_code']) && $conditions['short_code']) {
                $data = $data->where('short_code', $conditions['short_code']);
            }

            // get max date
            if (isset($conditions['maxDate']) && $conditions['maxDate']) {
                return $data->orderBy('updated_at', 'desc')->first();
            }

            // get max date
            if (isset($conditions['phap_nhan_in']) && $conditions['phap_nhan_in']) {
                $data = $data->whereIn('short_code', $conditions['phap_nhan_in']);
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