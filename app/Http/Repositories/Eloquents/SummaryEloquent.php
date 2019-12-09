<?php

namespace App\Http\Repositories\Eloquents;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Summary;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SummaryEloquent extends BaseEloquent implements SummaryRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Summary::class;
    }


    /**
     * @param array $conditions
     * @param bool $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true)
    {
        $data = $this->model;
//        dd($conditions);
        if ($conditions) {
            if (isset($conditions['select']) && $conditions['select']) {
                $data = $data->selectRaw($conditions['select']);
            }
            //search
            if (isset($conditions['search']) && $conditions['search']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->whereHas('employees', function ($q1) use ($conditions) {
                        $name = slipName($conditions['search']);
                        $q1->where('first_name', 'like', '%' . $name['first_name'] . '%');
                        if (isset($name['last_name']))
                            $q1->orWhere('last_name', 'like', '%' . $name['last_name'] . '%');
                        $q1->orWhere('employee_code', $conditions['search']);
                    })->orWhereHas('employeeRent', function ($q1) use ($conditions) {
                        $q1->where('identity_code', $conditions['search'])
                            ->orWhere('emp_name', 'like', '%' . $conditions['search'] . '%');
                    });
                });
            }
            //tim kiem theo ngay thanh toan trong order info
            if (isset($conditions['order_ngay_thanh_toan']) && $conditions['order_ngay_thanh_toan']) {
                $data = $data->whereHas('order', function ($q) use ($conditions) {
//                    $q->whereMonth('created_at', $conditions['order_ngay_thanh_toan']);
                });
            }
            // tim kiếm summary theo employee
            if (isset($conditions['employee']) && $conditions['employee']) {
                if ($conditions['employee'] instanceof Employee) {
                    $employee = $conditions['employee'];
                    $data = $data->where(function ($q) use ($employee) {
                        $q->where('employee_code', $employee->employee_code)
                            ->orWhere('employee_code', $employee->cmt);
                    });
                } else {
                    $data = $data->where('employee_code', 0);

                }
            }

            //tim kiem theo bo thanh toan trong bang doi soat bang info_id
            if (isset($conditions['order_cross_check_by_info_id']) && $conditions['order_cross_check_by_info_id']) {
                $crossInfoId = $conditions['order_cross_check_by_info_id'];
                $data = $data->whereRaw('summary.order_id in (select order_id from cross_checks where cross_checks.info_id = '.$crossInfoId.' and order_id is not null)');
            }

            //tim kiem theo ngay thanh toan
            if (isset($conditions['ngay_thanh_toan']['month']) && $conditions['ngay_thanh_toan']['month']) {
                $data = $data->whereMonth('ngay_thanh_toan', $conditions['ngay_thanh_toan']['month']);
            }
            if (isset($conditions['ngay_thanh_toan']['year']) && $conditions['ngay_thanh_toan']['year']) {
                $data = $data->whereYear('ngay_thanh_toan', $conditions['ngay_thanh_toan']['year']);
            }
            // tim kiem theo phap nhan
            if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
                $data = $data->where('phap_nhan', $conditions['phap_nhan']);
            }

            // tim kiem theo thang
            if (isset($conditions['month']) && $conditions['month']) {
                $conditions['month'] = intval($conditions['month']);
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('month', $conditions['month'])->orWhere('month', intval($conditions['month']));
                });
            }
            // tim kiem theo nam
            if (isset($conditions['year']) && $conditions['year']) {

                if (isset($conditions['orNullMonthYear']) && $conditions['orNullMonthYear']) {
                    $data = $data->where(function ($q) use ($conditions) {
                        $q->where('year', $conditions['year'])->orWhere(function ($q1) {
                            $q1->whereNull('month')->whereNull('year');
                        });
                    });
                } else {
                    $data = $data->where('year', $conditions['year']);
                }

            }
            // tim kiem theo employee_code
            if (isset($conditions['employee_code']) && $conditions['employee_code']) {
                if (is_array($conditions['employee_code'])) {
                    $data = $data->whereIn('employee_code', $conditions['employee_code']);
                } else {
                    $data = $data->where('employee_code', $conditions['employee_code']);
                }
            }
            // tìm kiếm theo order_id
            if (isset($conditions['order_id']) && $conditions['order_id']) {
                $data = $data->where(['order_id' => $conditions['order_id']]);
            }
            if (isset($conditions['month_created_at']) && $conditions['month_created_at']) {
                $data = $data->whereMonth('created_at', $conditions['month_created_at']);
                $data = $data->whereNull('month');
            }
            if (isset($conditions['year_created_at']) && $conditions['year_created_at']) {
                $data = $data->whereYear('created_at', $conditions['year_created_at']);
                $data = $data->whereNull('year');
            }

            // get dữ liệu quan hệ
            if (isset($conditions['with']) && $conditions['with']) {
                $data = $data->with($conditions['with']);
            }

            if (isset($conditions['group_by']) && $conditions['group_by']) {
                $data = $data->groupBy($conditions['group_by']);
            }
            if (isset($conditions['group_by_raw']) && $conditions['group_by_raw']) {
                $data = $data->groupByRaw($conditions['group_by_raw']);
            }
            if (isset($conditions['order_by_raw']) && $conditions['order_by_raw']) {
                $data = $data->orderByRaw($conditions['order_by_raw']);
            }

//            d($data->toSql());
            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {

                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }

            if (isset($conditions['type'])) {
                $type = app(TypeRepositoryInterface::class)->getDataBy([], false)->firstWhere('name', $conditions['type']);
                if (isset($type->id)) {
                    $data = $data->where('type', $type->id);
                }
            }
        }
//        dd($data->get());
        if (isset($conditions['status'])) {
            if ($conditions['status'] !== 'all') {
                $data = $data->where('status', $conditions['status']);
            }
        } else {
            $data = $data->where('status', 1);
        }

//        dd($data->get());
        if ($pagination) {
            return $data->paginate(ITEM_NUMBER);
        } else {
            return $data->get();
        }
    }

    public function saveDataByOrderId($orderId, $newData)
    {
        $data = $this->model;
        return $data->where(['order_id' => $orderId])->update($newData);
    }

    public function saveData($data)
    {
        if (!isset($data['id'])) {
            $data['id'] = 0;
        }
        if (defined('UPDATE_STATUS_SALARY') && UPDATE_STATUS_SALARY) {
            if ($data['type'] == 1) {
                $data['status'] = 1;
                $data['ngay_thanh_toan'] = data['year'] . '-' . $data['month'] . '01 00:00:00';
            }
        }

        $order = $this->model->firstOrNew(['id' => $data['id']])->fill($data);
        $order->save();
        return $order;
    }

    public function saveManyData($data)
    {
        foreach ($data as $d) {
            $this->saveData($d);
        }
        return true;
    }

    public function delete($conditions)
    {
        $data = $this->model;
        $check = false;

        if ($conditions) {
            if (isset($conditions['id']) && $conditions['id']) {
                $check = true;
                if (is_array($conditions['id'])) {
                    $data = $data->whereIn('id', $conditions['id']);
                } else {
                    $data = $data->where('id', $conditions['id']);
                }
            }
            if (isset($conditions['status'])) {
                $check = true;
                $data = $data->where('status', $conditions['status']);
            }
            if (isset($conditions['phap_nhan'])) {
                $check = true;
                $data = $data->where('phap_nhan', $conditions['phap_nhan']);
            }
            if (isset($conditions['month'])) {
                $check = true;
                $data = $data->where('month', $conditions['month']);
            }
            if (isset($conditions['year'])) {
                $check = true;
                $data = $data->where('year', $conditions['year']);
            }
            if (isset($conditions['type'])) {
                $check = true;
                $type = app(TypeRepositoryInterface::class)->getDataBy([], false)->firstWhere('name', $conditions['type']);
                if (isset($type->id)) {
                    $data = $data->where('type', $type->id);
                }
            }
        }
        if ($check)
            return $data->delete();
    }

    public function getReportData($conditions)
    {
        $data = $this->model;
        if (isset($conditions['select']) && $conditions['select']) {
            $data = $data->selectRaw($conditions['select']);
        }
        if (isset($conditions['ngay_thanh_toan']['month']) && $conditions['ngay_thanh_toan']['month']) {
            $data = $data->where('month', $conditions['ngay_thanh_toan']['month']);
        }
        if (isset($conditions['ngay_thanh_toan']['year']) && $conditions['ngay_thanh_toan']['year']) {
            $data = $data->where('year', $conditions['ngay_thanh_toan']['year']);
        }
        if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
            $data = $data->where('phap_nhan', $conditions['phap_nhan']);
        }

        $data = $data->join('type', 'type.id', '=', 'summary.type')
            ->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'summary.employee_code');
        $data = $data->where('summary.status', 1);
        if (isset($conditions['group_by']) && $conditions['group_by']) {
            if (is_array($conditions['group_by'])) {
                foreach ($conditions['group_by'] as $group_by) {
                    $data = $data->groupBy($group_by);
                }
            } else {
                $data = $data->groupBy($conditions['group_by']);
            }
        }
        // union
        if (isset($conditions['union_all'])) {
            $union = $this->model;
            if (isset($conditions['union_all']['select']) && $conditions['union_all']['select']) {
                $union = $union->selectRaw($conditions['union_all']['select']);
            }
            if (isset($conditions['ngay_thanh_toan']['month']) && $conditions['ngay_thanh_toan']['month']) {
                $union = $union->where('month', $conditions['ngay_thanh_toan']['month']);
            }
            if (isset($conditions['ngay_thanh_toan']['year']) && $conditions['ngay_thanh_toan']['year']) {
                $union = $union->where('year', $conditions['ngay_thanh_toan']['year']);
            }
            if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
                $union = $union->where('phap_nhan', $conditions['phap_nhan']);
            }
            if (isset($conditions['union_all']['where_add_or']) && $conditions['union_all']['where_add_or']) {
                $union = $union->where(function ($q) use ($conditions) {
                    foreach ($conditions['union_all']['where_add_or'] as $key => $whereOr) {
                        foreach ($whereOr as $type1) {
                            $q->orWhere($key, $type1);
                        }
                    }
                });
            }

            $union = $union->join('type', 'type.id', '=', 'summary.type')
                ->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'summary.employee_code');
            $union = $union->where('summary.status', 1);
            if (isset($conditions['union_all']['group_by']) && $conditions['union_all']['group_by']) {
                if (is_array($conditions['union_all']['group_by'])) {
                    foreach ($conditions['union_all']['group_by'] as $group_by) {
                        $union = $union->groupBy($group_by);
                    }
                } else {
                    $union = $union->groupBy($conditions['union_all']['group_by']);
                }
            }
//            d($union->toSql());
            $data = $data->union($union);
        }
        // union 1
        if (isset($conditions['union_all_1'])) {
            $union1 = $this->model;
            if (isset($conditions['union_all_1']['select']) && $conditions['union_all_1']['select']) {
                $union1 = $union1->selectRaw($conditions['union_all_1']['select']);
            }
            if (isset($conditions['ngay_thanh_toan']['month']) && $conditions['ngay_thanh_toan']['month']) {
                $union1 = $union1->where('month', $conditions['ngay_thanh_toan']['month']);
            }
            if (isset($conditions['ngay_thanh_toan']['year']) && $conditions['ngay_thanh_toan']['year']) {
                $union1 = $union1->where('year', $conditions['ngay_thanh_toan']['year']);
            }
            if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
                $union1 = $union1->where('phap_nhan', $conditions['phap_nhan']);
            }
            if (isset($conditions['union_all_1']['where_add_or']) && $conditions['union_all_1']['where_add_or']) {
                $union1 = $union1->where(function ($q) use ($conditions) {
                    foreach ($conditions['union_all_1']['where_add_or'] as $key => $whereOr) {
                        foreach ($whereOr as $type1) {
                            $q->orWhere($key, $type1);
                        }
                    }
                });
            }

            $union1 = $union1->join('type', 'type.id', '=', 'summary.type')
                ->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'summary.employee_code');
            $union1 = $union1->where('summary.status', 1);
            if (isset($conditions['union_all_1']['group_by']) && $conditions['union_all_1']['group_by']) {
                if (is_array($conditions['union_all_1']['group_by'])) {
                    foreach ($conditions['union_all_1']['group_by'] as $group_by) {
                        $union1 = $union1->groupBy($group_by);
                    }
                } else {
                    $union1 = $union1->groupBy($conditions['union_all_1']['group_by']);
                }
            }
//            d($union->toSql());
            $data = $data->union($union1);
        }

//        d($data->toSql());
        $data = $data->get();
//        dd($data->toArray());
        return $data;
    }

    public function get401ReportOfSalaryAndComBonus($conditions)
    {
        $data = $this->model->selectRaw("COUNT(DISTINCT summary.employee_code) AS tong_nv, SUM(case when type = 1 then sum_tnct else tong_tnct end) AS tong_tnct, 
COUNT(DISTINCT CASE WHEN (type != 1 and thue_tam_trich > 0) or (type =1 and sum_thue_tam_trich>0) THEN summary.employee_code ELSE NULL END) AS tong_nhan_su_nop_thue,
 SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN tong_tnct ELSE case when type=1 and sum_thue_tam_trich>0 then sum_tnct else 0 end END) AS tong_tnct_ns_nop_thue, 
 SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN thue_tam_trich ELSE case when type = 1 and sum_thue_tam_trich>0 then sum_thue_tam_trich else 0 end END) AS thue_tncn, 
CASE WHEN TYPE = 6 or TYPE = 5 or TYPE =1 THEN type.name ELSE \"khac1\" END AS TYPE1,
 CASE WHEN TYPE = 6 or TYPE = 5 or TYPE =1 THEN type.name ELSE \"khac1\" END AS name, 
 employee_rent.emp_name AS emp_rent_name, 
 employee_rent.emp_live_status AS live_status,
 sum(case when type = 1  then 1 else 0 end) as is_salary,
 sum(case when type = 6  then 1 else 0 end) as is_bonus,
 sum(case when type = 5  then 1 else 0 end) as is_com,
 sum(case when type = 6 or type = 5 then 1 else 0 end) as com_thuong")
            ->join('type', 'type.id', '=', 'summary.type')
            ->leftJoin('employee_rent', 'employee_rent.identity_code', '=', 'summary.employee_code')
            ->where('summary.status', 1)
            ->where('month', $conditions['month'])
            ->where('year', $conditions['year'])
            ->where('phap_nhan', $conditions['phap_nhan'])
            ->where(function ($q) {
                $q->where('summary.type', 1)->orWhere('summary.type', 5)->orWhere('summary.type', 6);
            })->groupBy('summary.employee_code');
        if ($conditions['is_salary'] == 1) {
            $data = $data->having('is_salary', 1);
        }
        if ($conditions['is_com'] == 1) {
            $data = $data->having('is_com', '>', 0)->having('is_salary', 0);
        }
        if ($conditions['is_bonus'] == 1) {
            $data = $data->having('is_bonus', '>', 0)->having('is_salary', 0);
        }
        if ($conditions['com_thuong'] == 1) {
            $data = $data->having('com_thuong', '>', 0)->having('is_salary', 0);
        }
        $report = DB::table(DB::raw("({$data->toSql()}) as sub"))->mergeBindings($data->getQuery())->selectRaw("
            sum(tong_nv) as tong_nv,
            sum(tong_tnct) as tong_tnct,
            sum(tong_nhan_su_nop_thue) as tong_nhan_su_nop_thue,
            sum(tong_tnct_ns_nop_thue) as tong_tnct_ns_nop_thue,
            sum(thue_tncn) as thue_tncn,
            name
        ")->first();
        return $report;
    }

    public function getReportData403($conditions)
    {
        $data = $this->model->selectRaw("
            case when summary.employee_table = 'employees' then employees.cmt else employee_rent.identity_code end as cmt1,
            case when summary.employee_table = 'employees' then concat(employees.last_name,' ',employees.first_name) else employee_rent.emp_name end as full,
            summary.ma_so_thue as mst,
            case when summary.employee_table = 'employees' then 1 else employee_rent.emp_live_status end as luu_tru,
            sum(summary.tong_tnct) as tong_tnct,
            sum(summary.giam_tru_gia_canh) as tong_so_thue_tncn_da_khau_tru,
            sum(case when (summary.type=1 || summary.type=5 || summary.type=6 || summary.type =10) then 1  else 0 end) as isSalary,
            case when summary.employee_table = 'employees' then employees.phap_nhan else '' end as phap_nhan_employees
        ")->leftJoin('employees', function ($q) {
            $q->on('employees.employee_code', '=', 'summary.employee_code')
                ->where('summary.employee_table', 'employees');
        })->leftJoin('employee_rent', function ($q1) {
            $q1->on('employee_rent.identity_code', '=', 'summary.employee_code')
                ->where('summary.employee_table', 'employee_rent');
        })->where('summary.phap_nhan', $conditions['phap_nhan'])
            ->whereYear('summary.ngay_thanh_toan', $conditions['year'])
            ->groupBy('cmt1')
            ->having('cmt1', '!=', '')
            ->having('isSalary', '=', 0)
            ->having('phap_nhan_employees', '!=', $conditions['phap_nhan'])
            ->get();
        return $data;

    }

    /**
     * @param $conditions
     * @param bool $pagination
     * @return mixed|object
     * @throws \Exception
     */
    public function getDataFttOfEmployee($conditions, $pagination = true)
    {
        if (count($conditions) != 4) {
            throw new \Exception('Sai giá trị truyền lên');
        }
        if (!(isset($conditions['employee_code']) && $conditions['employee_code'])) {
            throw new \Exception('Không xác định được employee_code');
        }
        if (!(isset($conditions['phap_nhan']) && $conditions['phap_nhan'])) {
            throw new \Exception('Không xác định được phap_nhan');
        }
        if (!(isset($conditions['month']) && $conditions['month'])) {
            throw new \Exception('Không xác định được month');
        }
        if (!(isset($conditions['year']) && $conditions['year'])) {
            throw new \Exception('Không xác định được year');
        }
        return $this->getDataBy($conditions, $pagination);
    }

    public function getAllFttOfEmployee($conditions)
    {
        if (count($conditions) != 4 && count($conditions) != 5) {
            throw new \Exception('Sai giá trị truyền lên');
        }
        if (!(isset($conditions['employee_code']) && $conditions['employee_code'])) {
            throw new \Exception('Không xác định được employee_code');
        }
        if (!(isset($conditions['phap_nhan']) && $conditions['phap_nhan'])) {
            throw new \Exception('Không xác định được phap_nhan');
        }
        if (!(isset($conditions['month']) && $conditions['month'])) {
            throw new \Exception('Không xác định được month');
        }
        if (!(isset($conditions['year']) && $conditions['year'])) {
            throw new \Exception('Không xác định được year');
        }
        $data = $this->model
            ->where('employee_code', $conditions['employee_code'])
            ->where('phap_nhan', $conditions['phap_nhan'])
            ->whereRaw('case when status = 0 and type !=1 and summary.month is null then month(created_at) = ' . $conditions['month'] . ' else summary.month = ' . $conditions['month'] . ' end')
            ->whereRaw('case when status = 0 and type !=1 and summary.year is null then year(created_at) = ' . $conditions['year'] . ' else summary.year = ' . $conditions['year'] . ' end');
        if (isset($conditions['is_additional_order']) && $conditions['is_additional_order'] == 'IsNotAddOrder') {
            $data = $data->whereHas('order', function ($q) {
                $q->where('additional_order', '0');
            });
        }

        if (isset($conditions['is_additional_order']) && $conditions['is_additional_order'] == 'IsAddOrder') {
            $data = $data->whereHas('order', function ($q) {
                $q->where('additional_order', '1');
            });
        }
        return $data->get();
    }

    public function getDataOfEmployee($conditions)
    {
        $data = $this->model;
        if (isset($conditions['select'])) {
            $data = $data->selectRaw($conditions['select']);
        }
        // tim kiếm summary theo employee
        if (isset($conditions['employee']) && $conditions['employee']) {
            if ($conditions['employee'] instanceof Employee) {
                $employee = $conditions['employee'];
                $data = $data->where(function ($q) use ($employee) {
                    $q->where('employee_code', $employee->employee_code)
                        ->orWhere('employee_code', $employee->cmt);
                });
            } else {
                $data = $data->where('employee_code', 0);
            }
        }
        if (isset($conditions['phap_nhan'])) {
            $data = $data->where('phap_nhan', $conditions['phap_nhan']);
        }
        if (isset($conditions['group_by'])) {
            $data = $data->groupBy($conditions['group_by']);
        }
        if (isset($conditions['is_additional_order']) && $conditions['is_additional_order'] == 'IsNotAddOrder') {
            $data = $data->whereHas('order', function ($q) {
                $q->where('additional_order', '0');
            });
        }
        if (isset($conditions['is_additional_order']) && $conditions['is_additional_order'] == 'IsAddOrder') {
            $data = $data->whereHas('order', function ($q) {
                $q->where('additional_order', '1');
            });
        }
        if (isset($conditions['order_by_raw'])) {
            $data = $data->orderByRaw($conditions['order_by_raw']);
        }
        if (isset($conditions['year'])) {
            $data = $data->having('year1', $conditions['year']);
        }
        if (isset($conditions['month'])) {
            $data = $data->having('month1', $conditions['month']);
        }
        return $data->get();
    }

    public function summaryOrderEmpData($phapnhan = '', $month = '', $year='')
    {
        $data = $this->model->selectRaw("
            summary.employee_code,
            summary.employee_table,
            summary.phap_nhan,
            summary.san_pham,
            summary.ma_so_thue,
            summary.tong_thu_nhap_truoc_thue,
            summary.tong_non_tax,
            summary.tong_tnct,
            summary.bhxh,
            summary.thue_tam_trich,
            summary.thuc_nhan,
            summary.giam_tru_ban_than,
            summary.giam_tru_gia_canh,
            summary.type,
            summary.note,
            summary.ref,
            summary.noi_dung,
            summary.status,
            summary.vi_tri,
            summary.cdt,
            summary.order_id,
            summary.ngay_thanh_toan,
            summary.month,
            summary.year,
            order_info.id as order_id,
            order_info.serial,
            order_info.noi_dung,
            type.name as type_name,
            case when summary.employee_table = 'employees' then concat(employees.last_name,' ',employees.first_name) else employee_rent.emp_name end as full_name,
            employees.first_name,employee_rent.emp_name,
            order_info.serial");
        
        $data->leftJoin('employees', function ($q) {
            $q->on('employees.employee_code', '=', 'summary.employee_code')
                ->where('summary.employee_table', 'employees');
        })->leftJoin('employee_rent', function ($q1) {
            $q1->on('employee_rent.identity_code', '=', 'summary.employee_code')
                ->where('summary.employee_table', 'employee_rent');
        })->leftJoin('order_info','order_info.id', '=', 'summary.order_id')
        ->leftJoin('type', 'type.id', '=', 'summary.type')->where('summary.status', 1);

        if($month) {
            $data->where('summary.month', $month);
        }

        if($phapnhan) {
            $data->where('summary.phap_nhan', $phapnhan);
        }
        if($year) {
            $data->where('summary.year', $year);
        }
        
        return $data;
        
    }
    public function getPTofAuth(){
        $employee = Employee::where('email',Auth::user()->email)->first();
        return $this->model->where('employee_code',$employee->employee_code)->pluck('phap_nhan');
    }

}


?>