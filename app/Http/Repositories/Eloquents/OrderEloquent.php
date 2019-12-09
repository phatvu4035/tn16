<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Repositories\Eloquents\EmployeeOrderEloquent;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Models\CrossCheck;
use App\Models\OrderInfo;
use App\Models\Summary;
use App\Models\Type;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderEloquent extends BaseEloquent implements OrderRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return OrderInfo::class;
    }

    /**
     * get data paginator
     * @param array $conditions
     * @param boolean $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true, $withSum = false)
    {
        $pageSize = ITEM_NUMBER;
        $scope = config('global.allow_order_filters');
        $scope = preg_filter('/^/', 'order_info.', $scope);
        $data = new $this->model;

//                if ($withSum) {
//                    array_push($scope, 'sum(employees_order.tncn) as thue');
//                    array_push($scope, '(order_info.so_tien - sum(employees_order.tncn)) as thuc_nhan');
//                    $data = $data
//                        ->join('employees_order', 'employees_order.order_id', '=', 'order_info.id')
//                        ->selectRaw(implode(",", $scope))
//                        ->groupBy("order_id");
//                }
        // @TODO đổi bảng
        if ($withSum) {
            array_push($scope, 'sum(summary.tong_tnct) as tong_tnct');
            array_push($scope, 'sum(summary.thue_tam_trich) as thue');
            array_push($scope, 'sum(summary.thuc_nhan) as thuc_nhan');
            array_push($scope, 'users.name as nguoi_tao');
            array_push($scope, 'order_info.created_by as me');
            array_push($scope, 'concat(employees.last_name," ",employees.first_name) as nguoi_de_xuat');


            $data = $data
                ->join('summary', 'summary.order_id', '=', 'order_info.id')
                ->leftJoin('users', 'users.id', '=', 'order_info.created_by')
                ->leftJoin('employees', 'employees.employee_code', '=', 'order_info.nguoi_de_xuat')
                ->selectRaw(implode(",", $scope))
                ->groupBy("order_id");
        }
        if ($conditions) {
            if (isset($conditions['search']) && trim($conditions['search']) !== "") {
                $searchValue = $conditions['search'];
                $data = $data->where(function ($q) use ($searchValue) {
                    $q->where("order_info.ma_du_toan", "like", "%$searchValue%")
                        ->orWhere("order_info.noi_dung", "like", "%$searchValue%")
                        ->orWhere("order_info.serial", "like", "%$searchValue%")
                        ->orWhere("order_info.id", "$searchValue");
                });
            }
            if (isset($conditions['filters'])) {
                $filters = $conditions['filters'];

                $allowFilters = config('global.allow_order_filters');

                foreach ($filters as $key => $value) {
                    if (!isset($value['field'])) {
                        continue;
                    }
                    if (in_array($value['field'], $allowFilters) || true == true) {
                        if (!isset($value['value'])) {
                            continue;
                        }
                        if (isset($value['operator'])) {
                            if (strtolower($value['operator']) == "in") {
                                $data = $data->whereIn($value['field'], $value['value']);
                            } else {
                                $data = $data->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $data = $data->where($value['field'], $value['value']);
                        }
                    }
                }

            }

            // get by ID
            if (isset($conditions['id']) && $conditions['id']) {
                $data = $data->where('id', $conditions['id']);
            }
            if (isset($conditions['created_by']) && $conditions['created_by']) {
                $data = $data->where('order_info.created_by', $conditions['created_by']);
            }

            if (isset($conditions['isSalary']) && $conditions['isSalary']) {
                $data = $data->where('isSalary', $conditions['isSalary']);
            }

            if (isset($conditions['month']) && $conditions['month']) {
                $data = $data->where('month', $conditions['month']);
            }

            if (isset($conditions['year']) && $conditions['year']) {
                $data = $data->where('year', $conditions['year']);
            }

            if (isset($conditions['order_info.id']) && $conditions['order_info.id']) {
                $data = $data->where('order_info.id', $conditions['order_info.id']);
            }
            

            if (isset($conditions['serial']) && $conditions['serial']) {
                $data = $data->where('serial', $conditions['serial']);
            }
            if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
                $data = $data->where('phap_nhan', $conditions['phap_nhan']);
            }
            if (isset($conditions['status']) && $conditions['status']) {
                $data = $data->where('status', $conditions['status']);
            }
            if (isset($conditions['notId']) && $conditions['notId']) {
                $data = $data->where('id', '!=', $conditions['notId']);
            }
            if (isset($conditions['per_page']) && $conditions['per_page']) {
                $pageSize = intval($conditions['per_page']);
            } else {
                $pageSize = ITEM_NUMBER;
            }

            if (isset($conditions['selectRawJoin']) && $conditions['selectRawJoin']) {
                $raw = $conditions['selectRawJoin'];
                $data = $data->selectRaw($raw);
                $data = $data->join('summary', 'summary.order_id', '=', 'order_info.id');
            }

            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {
                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
        }
        //sort data
        if (isset($conditions['sorters']) && $conditions['sorters']) {
            if (is_array($conditions['sorters'])) {
                foreach ($conditions['sorters'] as $key => $value) {
                    $data = $data->orderBy($value['field'], $value["dir"]);
                }
            }
        } else {
            $data = $data->orderBy('order_info.id', 'DESC');
        }
//        if (isset($conditions['selecRaw'])) {
//            d($data->toSql());
//        }
        if ($pagination) {
            return $data->paginate($pageSize);
        } else {
            return $data->get();
        }
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    private function canEdit($model, $data)
    {
        $result = [
            'result' => true,
            'message' => "OK"
        ];
        if (!$model->exists) {
            return $result;
        }
        //Nếu bộ thanh toán không tồn tại trong bộ đối soát
        if (!CrossCheck::where('order_id', '=', $model->id)->where('temp_order', '=', 0)->exists()) {
            return $result;
        }

        if ($model->serial != $data['serial'] && $model->status == OrderInfo::CROSS_CHECK_DONE) {
            $result['result'] = false;
            $result['message'] = "Bạn không thể sửa serial bộ thanh toán đã đối soát";
        }

        if ($model->so_tien != $data['so_tien']
            || $model->loai_tien != $data['loai_tien']
            || $model->ty_gia != $data['ty_gia']
            || $model->quy_doi != $data['quy_doi']) {
            $result['result'] = false;
            $result['message'] = "Bạn không thể sửa thông tin <b>số tiền trong bộ thanh toán</b> vì bộ thanh toán này đang <b>nằm trong bộ đối soát</b>";
        }

        return $result;
    }

    public function saveData($data, $summary = false)
    {
        if (isset($data['ngay_nhan_chung_tu'])) {
            $data['ngay_nhan_chung_tu'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['ngay_nhan_chung_tu'])));
        }
        if (isset($data['ngay_de_xuat'])) {
            $data['ngay_de_xuat'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['ngay_de_xuat'])));
        }

        if (!isset($data['id'])) {
            $data['id'] = 0;
        }

        //get list order type
        $type = app(TypeRepositoryInterface::class)->getDataBy([], false);
        $order = $this->model->firstOrNew(['id' => $data['id']]);
        $canEdit = $this->canEdit($order, $data);
        if (!$canEdit['result']) {
            throw new \Exception($canEdit['message']);
        }

        if (array_key_exists("additional_order", $data) && $data['additional_order'] == null) {
            $data['additional_order'] = 0;
        }

        if ($order['phap_nhan'] != $data['phap_nhan']) {
            app(SummaryRepositoryInterface::class)->saveDataByOrderId($data['id'] ,[
                'phap_nhan' => $data['phap_nhan']
            ]);
        }

        $order = $order->fill($data);
        $order->save();
        if ($summary) {
            $dataSummary = [];
            foreach ($summary as $e_order) {

                $s = new Summary(
                    [
                        'phap_nhan' => $order['phap_nhan'],
                        'san_pham' => $order['san_pham'],
                        'type' => ($type->firstWhere('name', $e_order['note'])) ? $type->firstWhere('name', $e_order['note'])->id : 0
                    ]
                );
                if (defined('UPDATE_STATUS_SALARY') && UPDATE_STATUS_SALARY) {
//                    dd($s);
                    if ($s['type'] == 1) {
                        $s['status'] = 1;
                        $s['ngay_thanh_toan'] = $e_order['year'] . '-' . $e_order['month'] . '-01 00:00:00';
                    }
                }
                $s->fill($e_order);
                $dataSummary[] = $s;
            }
            $order->summary()->saveMany($dataSummary);
        }
        return $order;
    }

    public function delete($conditions = [])
    {
        $data = $this->model;
        $check = false;
        if ($conditions) {
            // delete by ID
            if (isset($conditions['id']) && $conditions['id']) {
                if (is_array($conditions['id'])) {
                    $data = $data->whereIn("id", $conditions['id']);
                } else {
                    $order = OrderInfo::where("id", $conditions['id'])->first();
                    if (empty($order)) {
                        $result['result'] = false;
                        $result['message'] = "Bộ thanh toán không tồn tại";
                        return $result;
                    }

                    if ($order->status == 1) {
                        $result['result'] = false;
                        $result['message'] = "Bạn không thể xóa <b>bộ thanh toán</b> vì bộ thanh toán này đang <b>nằm trong bộ đối soát</b>";
                        return $result;
                    }

                    $data = $data->where('id', $conditions['id']);
                }
                $check = true;
            }

            // delete by serial
            if (isset($conditions['noSerial']) && $conditions['noSerial']) {
                $data = $data->where(function ($q) {
                    $q->where("serial", "=", "")
                        ->orWhereNull("serial");
                });
//                $data = $data->where('serial', $conditions['serial']);
                $check = true;
            }
        }
        return $check ? $data->delete() : null;
    }

    public function updateStatus($id, $data, $ignoreException = false)
    {
        $order = $this->model->where(['id' => $id])->first();
        if (!$order && !$ignoreException) {
            throw new \Exception("Không tìm thấy bộ thanh toán");
        }

        $order->status = $data['status'];
        $order->ngay_thanh_toan = $data['ngay_thanh_toan'];
        if ($order->isSalary == 0) {
            $data['month'] = null;
            $data['year'] = null;
            if ($data['ngay_thanh_toan']) {
                $data['month'] = intval(\DateTime::createFromFormat("Y-m-d H:i:s", $data['ngay_thanh_toan'])->format('m'));
                $data['year'] = intval(\DateTime::createFromFormat("Y-m-d H:i:s", $data['ngay_thanh_toan'])->format('Y'));
            }
            $order->month = $data['month'];
            $order->year = $data['year'];
        } else if ($order->isSalary == 1) {
            if (isset($data['status']) && $data['status'] == OrderInfo::CROSS_CHECK_DONE && isset($data['dot_thanh_toan'])) {
                $order->dot_thanh_toan = $data['dot_thanh_toan'];
            } else {
                $order->dot_thanh_toan = null;
            }
            if (isset($data['dot_thanh_toan'])) unset($data['dot_thanh_toan']);
        }
        $order->save();
        $order->summary()->update($data);

        Log::info(Auth::user()->name . " cập nhật trạng thái bộ thanh toán ".$id." thành ". $order->status);

        return $order;
    }
}