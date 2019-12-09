<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\CrossCheckYearRepositoryInterface;
use App\Models\CrossCheckYear;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class CrossCheckYearEloquent extends BaseEloquent implements CrossCheckYearRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return CrossCheckYear::class;
    }

    /**
     * get data paginator
     * @param array $conditions
     * @param boolean $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true)
    {
        $data = new CrossCheckYear();
        $pageSize = ITEM_NUMBER;
        if ($conditions) {
            if (isset($conditions['order_id']) && $conditions['order_id']) {
                $val = $conditions['order_id'];
                $data = $data->where("order_id", $val);
            }

            if (isset($conditions['month']) && $conditions['month']) {
                $val = intval($conditions['month']);
                if ($val >= 1 && $val <= 12) {
                    $data = $data->whereMonth("ngay_chung_tu", "=", $conditions['month']);
                }
            }

            if (isset($conditions['id']) && $conditions['id']) {
                $val = $conditions['id'];
                $data = $data->where("id", $val);
            }

            if (isset($conditions['info_id']) && $conditions['info_id']) {
                $val = $conditions['info_id'];
                $data = $data->where("info_id", $val);
            }

            if (array_key_exists('serial', $conditions)) {
                $val = $conditions['serial'];
                if ($val == "null") {
                    $data = $data->where(function ($q) {
                        $q->where("serial", "=", "")
                            ->orWhereNull("serial");
                    });
                } else {
                    $data = $data->where("serial", $val);
                }
            }

            //filter data
            if (isset($conditions['filters'])) {
                $filters = $conditions['filters'];
//                d($filters);
                foreach ($filters as $key => $value) {
                    if (!isset($value['field']) || $value['field'] == "temp_order") {
                        continue;
                    }

                    if (isset($value['type'])) {
                        $value['operator'] = $value['type'];
                    }

                    if (isset($value['operator'])) {
                        if (strtolower($value['operator']) == "in") {
                            $data = $data->whereIn($value['field'], $value['value']);
                        } else {
                            if ($value['value'] == null) {
                                $data = $data->whereNull($value['field']);
                            } else {
                                if (strtolower($value['operator']) == "like") {
                                    $value['value'] = "%".$value['value']."%";
                                }
                                $data = $data->where($value['field'], $value['operator'], $value['value']);
                            }
                        }
                    } else {
                        if ($value['value'] == null) {
                            $data = $data->whereNull($value['field']);
                        } else {
                            $data = $data->where($value['field'], $value['value']);
                        }
                    }
                }
            }

//            if (isset($conditions['temp_order'])) {
//                $val = $conditions['temp_order'];
//                if ($val == 0) {
//                    $data = $data->where("temp_order", 0);
//                } else {
//                    $data = $data->where(function ($q) use ($val) {
//                        return $q->whereNull("order_id")
//                            ->orWhere(function ($q2) use ($val) {
//                                return $q2->whereNotNull("order_id")->where("temp_order", $val);
//                            });
//                    });
//                }
//            }

            //sort data
            if (isset($conditions['sort']) && $conditions['sort']) {
                if (is_array($conditions['sort'])) {
                    foreach ($conditions['sort'] as $key => $value) {
                        if (!isset($value['field']) || $value['field'] == "temp_order") {
                            continue;
                        }
                        if ($value['field'] == "order_id") {
                            if (strtolower($value["dir"] == 'asc')) {
                                $data = $data->orderBy("order_id", 'asc');
                                $data = $data->orderBy("active", 'desc');
                            } else {
                                $data = $data->orderBy("order_id", 'desc');
                            }
                        } else {
                            $data = $data->orderBy($value['field'], $value["dir"]);
                        }
                    }
                }
            }

            //per page
            if (isset($conditions['per_page']) && $conditions['per_page']) {
                $pageSize = intval($conditions['per_page']);
            }

            if (isset($conditions['with_order']) && $conditions['with_order']) {
                $data = $data->with('order');
                $data = $data->with('tax');
            }

            if (isset($conditions['with_month_order']) && $conditions['with_month_order']) {
                $data = $data->with('monthOrder');
                $data = $data->with('monthTax');
            }

            if (isset($conditions['nam']) && $conditions['nam']) {
                $nam = $conditions['nam'];
                $data = $data->whereYear("ngay_chung_tu", $nam);
            }

            if (!isset($conditions['with_active'])) {
                $data = $data->where("active", 1);
            }

            if (isset($conditions['selecRaw']) && $conditions['selecRaw']) {
                $raw = $conditions['selecRaw'];
                $data = $data->selectRaw($raw);
            }

            if (isset($conditions['count']) && $conditions['count']) {
                return $data->count();
            }

            if (isset($conditions['count']) && $conditions['count']) {
                return $data->count();
            }

            if (isset($conditions['yearUndone']) && $conditions['yearUndone']) {
                $yearModel = new CrossCheckYear();
                $yearModel = $yearModel->selectRaw("count(id) as `tong`, sum(IF(order_id is null,1,0)) as `chua_doi_soat`, YEAR(STR_TO_DATE(ngay_chung_tu, \"%Y-%m-%d %H:%i:%s\")) as `nam`")
                    ->where("phap_nhan", $conditions['yearUndone'])->where("active", 1)->groupBy    (DB::raw("YEAR(STR_TO_DATE(ngay_chung_tu, \"%Y-%m-%d %H:%i:%s\"))"));
                return $yearModel->get()->toArray();
            }

            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {
                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }

            if ($pagination) {
                return $data->paginate($pageSize);
            }
        }
//        d($data->toSql());
        return $data->get();
    }

    public function saveData($data, $multiple = false)
    {
        if ($multiple) {
            return CrossCheckYear::insert($data);
        }
        if (isset($data['order_id']) && $data['order_id'] != null) {
            $data['tcb_id'] = \Auth::user()->id;
        }
        if (isset($data['id']) && $data['id']) {
            $crossCheck = $this->model->find($data['id'])->fill($data);
        } else {
            $crossCheck = new CrossCheckYear;
            $crossCheck->fill($data);
        }
        $crossCheck->save();
        return $crossCheck;
    }

    public function updateOrderId($id, $orderId, $tempOrder = false) {
        $crossCheck = new CrossCheckYear;
        $check = false;
        if (is_array($id)) { // update cross check by conditions
            if (isset($id['info_id']) && $id['info_id']) {
                $val = $id['info_id'];
                $crossCheck = $crossCheck->where("info_id", $val);
                $check = true;
            }

            if (isset($id['order_id']) && $id['order_id']) {
                $val = $id['order_id'];
                $crossCheck = $crossCheck->where("order_id", $val);
                $check = true;
            }
        } else { // update cross check by id
            $crossCheck = $crossCheck->where(['id' => $id]);
            $check = true;
        }

        $tcb_id = null;
        if ($orderId != null && ($tempOrder == 0 || $tempOrder == false)) {
            $tcb_id = \Auth::user()->id;
        }

        return $check ? $crossCheck->update([
            "order_id" => $orderId,
            "tcb_id" => $tcb_id
        ]) : null;
    }

    public function delete($conditions)
    {
        $data = new CrossCheckYear();
        $check = false;

        if ($conditions) {
            if (isset($conditions['order_id']) && $conditions['order_id']) {
                $val = $conditions['order_id'];
                $data = $data->where("order_id", $val);
                $check = true;
            }

            if (isset($conditions['month']) && $conditions['month']) {
                $val = intval($conditions['month']);
                if ($val >= 1 && $val <= 12) {
                    $data = $data->whereMonth("ngay_chung_tu", "=", $conditions['month']);
                    $check = true;
                }
            }

            if (isset($conditions['info_id']) && $conditions['info_id']) {
                $val = $conditions['info_id'];
                $data = $data->where("info_id", $val);
                $check = true;
            }
        }

        return $check ? $data->delete() : null;
    }

    public function destroy($id)
    {

    }
}