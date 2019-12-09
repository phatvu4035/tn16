<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\EmployeeOrderRepositoryInterface;
use App\Models\EmployeeOrder;
use Illuminate\Pagination\Paginator;


class EmployeeOrderEloquent extends BaseEloquent implements EmployeeOrderRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return EmployeeOrder::class;
    }

    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true)
    {
        $data = $this->model;
        if ($conditions) {
            if (isset($conditions['order_id']) && $conditions['order_id']) {
                $data = $data->where(['order_id' => $conditions['order_id']]);
            }

            if (isset($conditions['with']) && $conditions['with']) {
                $data = $data->with($conditions['with']);
            }


        }


        // Check page number
        if (isset($conditions['page']) && $conditions['page']) {
            $currentPage = $conditions['page'];
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
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
                $employeeOrder = EmployeeOrder::find($d['id']);
            } else {
                $employeeOrder = new EmployeeOrder();
            }
            $employeeOrder->fill($d)->save();
        }
        return true;
    }

    public function deleteByOrderId($id) 
    {
        return EmployeeOrder::where("order_id", $id)->delete();
    }
}