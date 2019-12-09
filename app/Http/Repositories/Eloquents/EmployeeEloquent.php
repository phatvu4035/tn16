<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;


class EmployeeEloquent extends BaseEloquent implements EmployeeRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Employee::class;
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
            //get data by employee_code
            if (isset($conditions['employee_code']) && $conditions['employee_code']) {
                if (is_array($conditions['employee_code'])) {
                    $data = $data->whereIn('employee_code', $conditions['employee_code']);
                } else {
                    $data = $data->where('employee_code', $conditions['employee_code']);
                }
            }
            if (isset($conditions['cmt']) && $conditions['cmt']) {
                if (is_array($conditions['cmt'])) {
                    $data = $data->whereIn('cmt', $conditions['cmt']);
                } else {
                    $data = $data->where('cmt', $conditions['cmt']);
                }
            }
            //get data by employee_code or cmt/hc
            if (isset($conditions['identity_code']) && $conditions['identity_code']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('employee_code', $conditions['identity_code'])
                        ->orWhere('cmt', $conditions['identity_code']);
                });
            }
//            d($data->toSql());
            /**
             * Find by email
             */
            if (isset($conditions['email']) && $conditions['email']) {
                if (is_array($conditions['email'])) {
                    $data = $data->whereIn('email', $conditions['email']);
                } else {
                    $data = $data->where('email', $conditions['email']);
                }
            }
            // Find by full name
            if (isset($conditions['search']) && $conditions['search']) {
                $val = ($conditions['search']);
                $data = $data->selectRaw("employee_code as id,email,concat(last_name, ' ', first_name, ' (', employee_code, ')') as text");
                $data = $data->havingRaw('email like  "' . $val . '%" or text like "%' . $val . '%"');
            }
            // Set limit
            if (isset($conditions['limit']) && $conditions['limit']) {
                $val = intval($conditions['limit']) == 0 ? $conditions['limit'] : 20;
                $data = $data->limit($val);
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

    }

    public function createOrUpdateData($data)
    {
        $employee = $this->model->firstOrNew(['employee_code' => $data['employee_code']])->fill($data);
        $employee->save();
        return $employee;
    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function getReportData402($conditions)
    {
        $data = $this->model->selectRaw("
            concat(employees.last_name,' ',employees.first_name) as full,
            employees.employee_code,
            employees.cmt as cmt,
            sum(summary.tong_tnct)-sum(summary.bhxh)-IFNULL(SUM(summary.giam_tru_ban_than),0) - IFNULL(SUM(summary.giam_tru_ban_than),0)  as tong_tntt,
            sum(summary.tong_tnct) as tong_tnct,
            max(floor(summary.giam_tru_gia_canh/3600000)) as nguoi_phu_thuoc_giam_tru,
            sum(summary.giam_tru_gia_canh)+ sum(summary.giam_tru_ban_than) as tong_so_tien_giam_tru_gia_canh,
            sum(summary.bhxh) as bao_hiem_duoc_tru,     
            sum(summary.giam_tru_gia_canh) as tong_so_thue_tncn_da_khau_tru,
            sum(case when (summary.type=1 || summary.type=5 || summary.type=6) then 1  else 0 end) as isSalary,
	        employees.phap_nhan,
	        employees.mst as mst
       ")
            ->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'employees.cmt')
            ->join('summary', function ($q) {
                $q->on('summary.employee_code', '=', 'employees.employee_code')
                    ->orOn('summary.employee_code', '=', "employee_rent.identity_code");
            })
            ->where('summary.phap_nhan', $conditions['phap_nhan'])
            ->whereYear('summary.ngay_thanh_toan', $conditions['year'])
            ->groupBy('cmt')
            ->having('cmt', '!=', '')
            ->having('isSalary', '>', 0)
            ->get();
        return $data;

    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function getReportDataO1($conditions)
    {
        $data = $this->model->selectRaw("
            GROUP_CONCAT((select serial from order_info where id = summary.order_id)) as serial,
            CONCAT(employees.last_name,' ',employees.first_name) AS full,
            employees.employee_code,
            employees.cmt AS cmt,
            summary.phap_nhan,
            employees.mst,
            sum(case when summary.type = 5 then summary.tong_tnct else 0 end) as com_tnct,
            sum(case when summary.type = 5 then summary.thue_tam_trich else 0 end) as com_thue_tam_trich,
            sum(case when summary.type = 5 then summary.thuc_nhan else 0 end) as com_thuc_nhan,
            sum(case when summary.type = 6 then summary.tong_tnct else 0 end) as thuong_tnct,
            sum(case when summary.type = 6 then summary.thue_tam_trich else 0 end) as thuong_thue_tam_trich,
            sum(case when summary.type = 6 then summary.thuc_nhan else 0 end) as thuong_thuc_nhan,
            sum(case when summary.type != 6 and summary.type!=5 then summary.tong_tnct else 0 end) as other_tnct,
            sum(case when summary.type != 6 and summary.type!=5 then summary.thue_tam_trich else 0 end) as other_thue_tam_trich,
            sum(case when summary.type != 6 and summary.type!=5 then summary.thuc_nhan else 0 end) as other_thuc_nhan
        ")->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'employees.cmt')
            ->join('summary', function ($q) {
                $q->on('summary.employee_code', '=', 'employees.employee_code')
                    ->orOn('summary.employee_code', '=', "employee_rent.identity_code");
            })->where('summary.phap_nhan', $conditions['phap_nhan'])
            ->whereMonth('ngay_thanh_toan', $conditions['month'])
            ->whereYear('ngay_thanh_toan', $conditions['year'])
            ->where('summary.type', '!=', 1)
            ->groupBy('cmt')
            ->having('cmt', '!=', '')
            ->get();
        return $data;
    }

    public function getDataByEmployeeCodeAndCMT($employee)
    {
//        dd($employee);
        $data = $this->model;
        if (isset($employee['employee_code'])) {
            $data = $data->whereIn('employee_code', $employee['employee_code']);
        }
        if (isset($employee['id'])) {
            $data = $data->orwhereIn('cmt', $employee['id']);
        }
        if (isset($employee['ma_so_thue'])) {
            $data = $data->orwhereIn('mst', $employee['ma_so_thue']);
        }
        return $data->get();
    }

    /*
    * Lay ngay cuoi cung update nhan vien
    * 
    */
    public function getMaxUpdateDateEmployee()
    {
        $result = $this->model->orderBy('api_updated_time', 'desc')->limit(1)->get();
        if(count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }
}