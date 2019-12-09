<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Models\EmployeeRent;
use Illuminate\Pagination\Paginator;

class EmpRentEloquent extends BaseEloquent implements EmpRentRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return EmployeeRent::class;
    }

    /**
     * get data paginator
     * @param array $conditions
     * @param boolean $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true)
    {
        $data = $this->model;
        if ($conditions) {
            // Get data by working status
            if(isset($conditions['working_status']) && $conditions['working_status'] ) {
                $data = $this->dataByWorkingStatus($data, $conditions['working_status']);
            }
            
            // get data by search
            if (isset($conditions['search']) && $conditions['search']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('emp_name', 'like', '%' . $conditions['search'] . '%')
                        ->orWhere('identity_code', 'like', '%' . $conditions['search'] . '%');
                });
            }

            //get data by identity_code
            if (isset($conditions['identity_code']) && $conditions['identity_code']) {
                if (is_array($conditions['identity_code'])) {
                    $data = $data->whereIn('identity_code', $conditions['identity_code']);
                } else {
                    $data = $data->where('identity_code', $conditions['identity_code']);
                }
            }
            //get data by identity_code
            if (isset($conditions['identity_type']) && $conditions['identity_type']) {
                if (is_array($conditions['identity_type'])) {
                    $data = $data->whereIn('identity_type', $conditions['identity_type']);
                } else {
                    $data = $data->where('identity_type', $conditions['identity_type']);
                }
            }
            //get data by emp_tax_code
            if (isset($conditions['emp_tax_code']) && $conditions['emp_tax_code']) {
                if (is_array($conditions['emp_tax_code'])) {
                    $data = $data->whereIn('emp_tax_code', $conditions['emp_tax_code']);
                } else {
                    $data = $data->where('emp_tax_code', $conditions['emp_tax_code']);
                }
            }
            //get data by ID
            if (isset($conditions['id']) && $conditions['id']) {
                $data = $data->where('id', $conditions['id']);
            }

            if (isset($conditions['with_trash']) && $conditions['with_trash']==true) {
                $data = $data->withTrashed();
            }


        }

        // Get posts with order by column
        if (isset($conditions['orderby']) && $conditions['orderby']) {
            $data = $data->orderBy($conditions['orderby'], $conditions['order']);
        } else {
            $data = $data->orderBy('created_at', 'DESC');
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
        // format emp_code_date
        if (isset($data['emp_code_date']) && $data['emp_code_date']) {
            $data['emp_code_date'] = str_replace('/', '-', $data['emp_code_date']);
            $data['emp_code_date'] = date('Y-m-d H:i:s', strtotime($data['emp_code_date']));
        }
//        dd($data);
        if (isset($data['id']) && $data['id']) {

            if( isset($data['trashed']) && $data['trashed'] ) {
                $empRent = EmployeeRent::withTrashed()->where('id', $data['id'])->first();
            } else {
                $empRent = EmployeeRent::where('id', $data['id'])->first();
            }

        } elseif (isset($data['identity_code']) && $data['identity_code']) {

            if(isset($data['trashed']) && $data['trashed'] ) {
                $empRent = EmployeeRent::withTrashed()->where('identity_code', $data['identity_code'])->first();
            } else {
                $empRent = EmployeeRent::where('identity_code', $data['identity_code'])->first();
            }
            
        } else {
            $empRent = new EmployeeRent();
        }

        if (!($empRent)) {
            $empRent = new EmployeeRent();
        }
//        unset($data['id']);
        $empRent = $empRent->fill($data)->save();
        return $empRent;
    }

    public function destroy($id)
    {
        $this->model->destroy($id);
    }

    public function getDataByIdAndMst($employee)
    {
        $data = $this->model->withTrashed();
        if (isset($employee['id']) && $employee['id']) {
            $data = $data->whereIn('identity_code', $employee['id']);
        }
        if (isset($employee['ma_so_thue']) && $employee['ma_so_thue']) {
            $data = $data->orwhereIn('emp_tax_code', $employee['ma_so_thue']);
        }
        return $data->get();
    }

    /*
    * Get data by working status
    */
    public function dataByWorkingStatus($data, $select = null)
    {
        if($select == 'all') {
            $data = $data->withTrashed();
        } else if($select == 'non_working') {
            $data = $data->onlyTrashed();
        }
        return $data;
    }

    /*
    * Get trashed data
    */
    public function getAllData()
    {
        $data = $this->model;
        return $data->withTrashed();
    }

}