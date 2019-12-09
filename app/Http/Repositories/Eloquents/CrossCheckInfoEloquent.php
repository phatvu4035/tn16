<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\CrossCheckInfoRepositoryInterface;
use App\Models\CrossCheckInfo;
use Illuminate\Pagination\Paginator;

class CrossCheckInfoEloquent extends BaseEloquent implements CrossCheckInfoRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return CrossCheckInfo::class;
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
        $pageSize = ITEM_NUMBER;
        if ($conditions) {
            if (isset($conditions['phap_nhan']) && $conditions['phap_nhan']) {
                $val = $conditions['phap_nhan'];
                $data = $data->where("phap_nhan", $val);
            }

            if (isset($conditions['nam']) && $conditions['nam']) {
                $val = $conditions['nam'];
                $data = $data->where("nam", $val);
            }

            if (array_key_exists("thang", $conditions)) {
                $val = $conditions['thang'];
                if ($val == null) {
                    $data = $data->whereNull("thang");
                } else if($val == "notNull"){
                    $data = $data->whereNotNull("thang");
                } else {
                    $data = $data->where("thang", $val);
                }
            }

            if (isset($conditions['is_salary']) && $conditions['is_salary']) {
                $val = $conditions['is_salary'];
                $data = $data->where("is_salary", $val);
            }

            if (isset($conditions['ke_toan_check']) && $conditions['ke_toan_check']) {
                $val = $conditions['ke_toan_check'];
                $data = $data->where("ke_toan_check", $val);
            }

            if (isset($conditions['id']) && $conditions['id']) {
                $val = $conditions['id'];
                $data = $data->where("id", $val);
            }

            if (isset($conditions['withCrossChecks']) && $conditions['withCrossChecks']) {
                $scope = "(select count(id) from cross_checks where info_id = cross_check_info.id and active = 1 group by cross_check_info.id) as countCross";
                $scope .= ", (select count(id) from cross_checks where info_id = cross_check_info.id and order_id is null and active = 1 group by cross_check_info.id) as countUnDone";
                $data = $data->selectRaw("cross_check_info.*, ". $scope);
            }

            if (isset($conditions['yearUndone']) && $conditions['yearUndone']) {
                $yearModel = new CrossCheckInfo();
                $yearModel = $yearModel->selectRaw("SUM(IF(ke_toan_check = 0, 1, 0)) as `chua_doi_soat`, count(id) as `tong`,nam")
                    ->where("is_salary", 0)
                    ->where("phap_nhan", $conditions['yearUndone'])
                    ->groupBy(["nam", "phap_nhan"]);
                return $yearModel->get()->toArray();
            }

            if (isset($conditions['crossYearByPN']) && $conditions['crossYearByPN']) {
                $yearModel = new CrossCheckInfo();
                $yearModel = $yearModel->selectRaw("*")
                    ->where("thang", null)
                    ->where("phap_nhan", $conditions['crossYearByPN']);
                return $yearModel->get();
            }

            //sort data
            if (isset($conditions['sort']) && $conditions['sort']) {
                $data = $data->orderBy($conditions['sort']['field'], $conditions['sort']["direction"]);
            }

            //per page
            if (isset($conditions['per_page']) && $conditions['per_page']) {
                $pageSize = intval($conditions['per_page']);
            }

            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {
                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }

            if ($pagination) {
//                return $data->toSql();
                return $data->paginate($pageSize);
            }

            if (isset($conditions['first']) && $conditions['first']) {
                return $data->first();
            }
        }

        return $data->get();
    }

    public function delete($conditions = []) {
        $check = false;

        $data = new CrossCheckInfo();
        if (isset($conditions['id']) && $conditions['id']) {
            $val = $conditions['id'];
            $data = $data->where("id", $val);
            $check = true;
        }

        if ($check) {
            return $data->delete();
        } else {
            return false;
        }
    }

    public function saveData($data)
    {
        if (isset($data['ke_toan_check']) && ($data['ke_toan_check'] == 1 || $data['ke_toan_check'] == true)) {
            $data['ke_toan_id'] = \Auth::user()->id;
        }
        if (isset($data['id']) && $data['id']) {
            $crossCheck = $this->model->find($data['id'])->fill($data);
        } else {
            $crossCheck = $this->model->firstOrNew([
                "phap_nhan" => $data['phap_nhan'],
                "thang" => $data['thang'],
                "nam" => $data['nam'],
                "is_salary" => boolval($data['is_salary']) === true || $data['is_salary'] === 1 ? 1 : 0,
            ]);
            $crossCheck->fill($data);
        }
        $crossCheck->save();
        return $crossCheck;
    }
}