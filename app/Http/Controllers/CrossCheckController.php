<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\CrossCheckInfoRepositoryInterface;
use App\Http\Repositories\Contracts\CrossCheckYearRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Models\CrossCheckYear;
use App\Models\OrderInfo;
use Carbon\Carbon;
use App\Facades\Topica;
use DB;
use App\Helpers\ImportExcel;
use App\Http\Repositories\Contracts\CrossCheckRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Mockery\Exception;

class CrossCheckController extends Controller
{

    protected $importExcel;

    protected $crossCheckRepository;

    protected $crossCheckYearRepository;

    protected $orderRepository;

    protected $crossCheckInfoRepository;

    protected $summaryRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderRepositoryInterface $orderRepository, ImportExcel $importExcel, CrossCheckRepositoryInterface $crossCheckRepository, CrossCheckInfoRepositoryInterface $crossCheckInfoRepository, SummaryRepositoryInterface $summaryRepository, CrossCheckYearRepositoryInterface $crossCheckYearRepository)
    {
        $this->importExcel = $importExcel;
        $this->crossCheckRepository = $crossCheckRepository;
        $this->orderRepository = $orderRepository;
        $this->crossCheckInfoRepository = $crossCheckInfoRepository;
        $this->summaryRepository = $summaryRepository;
        $this->crossCheckYearRepository = $crossCheckYearRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "index page";
    }

    public function importPanel(Request $request, $luong, $phap_nhan, $thang, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check", $phap_nhan);
        return view("cross_check.import", compact("phap_nhan", "nam", "luong"));
    }

    public function proccessExcelData($data, $thang = null)
    {
        $mergeData = [];
        foreach ($data as $key => $value) {
            $month = intval(\DateTime::createFromFormat("d/m/Y", $value['ngay_chung_tu'])->format('m'));

            if ($month != $thang && $thang != null) {
                return Redirect::back()->with("message", [
                    'title' => 'Lỗi',
                    'content' => 'Tháng đối soát trong sổ không đúng với tháng đối soát đã chọn',
                    'type' => 'danger'
                ]);
            }
        }

        $taxs = [];

        //merge data cung seria
        foreach ($data as $key => $value) {
            foreach ($value as $t => $v) {
                if (endsWith($t, "_xbeat")) {
                    unset($value[$t]);
                }
            }
            if (startsWith($value['tai_khoan_doi_ung'], "3335")) {
                $id = empty($value['serial']) ? "no_".$key : $value['serial'];
                if (!isset($taxs[$id])) {
                    $taxs[$id] = $value;
                    $taxs[$id]['dien_giai'] = [$value['dien_giai']];
                } else {
                    $taxs[$id]['dien_giai'][] = $value['dien_giai'];
                    $taxs[$id]['ps_no'] += $value['ps_no'];
                }
                continue;
            }
            if (empty($value['serial'])) {
                $dienGiai = $value['dien_giai'];
                $value['dien_giai'] = [$dienGiai];
                //Y-m-d H:i:s.u
                $value['ngay_chung_tu'] = \DateTime::createFromFormat("d/m/Y", $value['ngay_chung_tu'])->format('Y-m-d H:i:s');
                $mergeData["no_".$key] = $value;
            } else {
                if (isset($mergeData[$value['serial']])) {
                    $mergeData[$value['serial']]['dien_giai'][] = $value['dien_giai'];
                    $psNo = $mergeData[$value['serial']]['ps_no'];
                    $psNoAdd = preg_replace('/[^0-9]/', '', $value['ps_no']);
                    $mergeData[$value['serial']]['ps_no'] = $psNo + $psNoAdd;
                } else {
                    $mergeData[$value['serial']] = $value;
                    $mergeData[$value['serial']]['ngay_chung_tu'] = \DateTime::createFromFormat("d/m/Y", $value['ngay_chung_tu'])->format('Y-m-d H:i:s');
                    $dienGiai = $mergeData[$value['serial']]['dien_giai'];
                    $mergeData[$value['serial']]['dien_giai'] = [];
                    $mergeData[$value['serial']]['dien_giai'][] = $dienGiai;
                    $psNo = $mergeData[$value['serial']]['ps_no'];
                    $mergeData[$value['serial']]['ps_no'] = intval(preg_replace('/[^0-9]/', '', $psNo));
                }
            }
        }
        $haveTax = [];

        foreach ($taxs as $key => $value) {
            if (!isset($mergeData[$key])) {
                $mergeData[$key] = $value;
                $mergeData[$key]['ngay_chung_tu'] = \DateTime::createFromFormat("d/m/Y", $value['ngay_chung_tu'])->format('Y-m-d H:i:s');
                $mergeData[$key]['thue'] = $mergeData[$key]['ps_no'];
                $mergeData[$key]['ps_no'] = 0;
                if (is_array($value['dien_giai'])) {
                    $mergeData[$key]['dien_giai'] = $value['dien_giai'];
                } else {
                    $mergeData[$key]['dien_giai'] = [$value['dien_giai']];
                }
                continue;
            }
            if (!isset($mergeData[$key]['tax'])) {
                $mergeData[$key]['thue'] = 0;
            }

            $mergeData[$key]['thue'] += $value['ps_no'];
            if (is_array($value['dien_giai'])) {
                $mergeData[$key]['dien_giai'] = array_merge($mergeData[$key]['dien_giai'], $value['dien_giai']);
            } else {
                $mergeData[$key]['dien_giai'][] = $value['dien_giai'];
            }
        }

        foreach ($mergeData as $key => $value) {
//            if ($value['ps_no'] == 0) {
//                unset($mergeData[$key]);
//            }

            if (!isset($value['thue'])) {
                $mergeData[$key]['thue'] = 0;
            }
        }

        return $mergeData;
    }

    public function importHandle(Request $request, $luong, $phap_nhan, $thang, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check",$phap_nhan);
        if (!$request->hasFile('importFile'))
        {
            return "File not found";
        }

        $file = $request->file('importFile');

        try {
            $data = $this->importExcel->getDataCrossCheck($file);
            if (empty($data)) {
                throw new \Exception("empty data");
            }
        } catch (\Exception $e) {
            return Redirect::back()->with("message", [
                'title' => 'Lỗi',
                'content' => 'File sai định dạng hoặc không có dữ liệu',
                'type' => 'danger'
            ]);
        }

        $mergeData = $this->proccessExcelData($data, $thang);
//        return response()->json($mergeData);

        if (!is_array($mergeData)) {
            return $mergeData;
        }

//        return response()->json($mergeData);

        DB::beginTransaction();
        try {
            $crossCheckData = [
                "phap_nhan" => $phap_nhan,
                "thang" => $thang,
                "nam" => $nam,
                "is_salary" => $luong == "luong" ? 1 : 0
            ];
            $crossCheckInfo = $this->crossCheckInfoRepository->saveData($crossCheckData);

            $this->crossCheckRepository->delete([
                'info_id' => $crossCheckInfo->id
            ]);
            foreach ($mergeData as $key => $value) {
                $value['dien_giai'] = json_encode($value['dien_giai'], JSON_UNESCAPED_UNICODE);
                $value['info_id'] = $crossCheckInfo->id;
                $value['phap_nhan'] = $crossCheckInfo->phap_nhan;
                $this->crossCheckRepository->saveData($value);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

        return redirect()->route("cross_check.showByMonth", [
            "luong" => $luong,
            "phap_nhan" => $phap_nhan,
            "thang" => $thang,
            "nam" => $nam,
        ])->with("message", [
            'title' => 'Thành công',
            'content' => 'Import sổ kế toán thành công',
            'type' => 'success'
        ]);
    }

    public function doneSalary(Request $request)
    {
        $requestData = $request->all();
        $isset = checkIsset($requestData, [
            'thang', 'nam', 'phap_nhan', 'is_salary', 'dates'
        ]);

        if ($isset !== true) {
            if ($isset == "dates") {
                return response()->json([
                    'title' => 'Thất bại',
                    'content' => 'Bạn chưa đợt thanh toán',
                    'type' => 'danger'
                ]);
            }
            throw new Exception($isset, 400);
        }

        Topica::canCrossOrAbort("export.cross_check_kt_check", $requestData['phap_nhan']);

        $dates = $requestData['dates'];

        usort($dates, function($v1, $v2) {
            $t1 = \DateTime::createFromFormat('d/m/Y', $v1)->getTimestamp();
            $t2 = \DateTime::createFromFormat('d/m/Y', $v2)->getTimestamp();
            return ($t1 - $t2);
        });

        if (empty($dates)) {
            return response()->json([
                'title' => 'Thất bại',
                'content' => 'Bạn chưa nhập ngày thanh toán',
                'type' => 'danger'
            ]);
        }

        $request->request->set("thanh-toan", $dates[count($dates) - 1]); // add thời gian thanh toán gần nhất
        $request->request->set("dates", $dates);

        $crossInfo = $this->crossCheckInfoRepository->saveData($requestData);

        $result = $this->doneAccounter($request, $crossInfo->id, true);

        return response()->json($result);
    }

    public function showByMonthSalary(Request $request, $luong, $phap_nhan, $thang, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $order = $this->orderRepository->getDataBy([
            'month' => $thang,
            'year' => $nam,
            'phap_nhan' => $phap_nhan,
            'isSalary' => 1
        ], false)->toArray();

        $conditions = [
            'thang' => $thang,
            'nam' => $nam,
            'phap_nhan' => $phap_nhan,
            'is_salary' => 1
        ];

        $info = $this->crossCheckInfoRepository->getDataBy($conditions, false)->toArray();

        if (empty($info)) {
            $this->crossCheckInfoRepository->saveData($conditions)->toArray();
            $info = $this->crossCheckInfoRepository->getDataBy($conditions, false)->toArray();
        }

        $isDone = empty($info) || $info[0]['ke_toan_check'] == 0 ? false : true;

        if (empty($order)) {
            return redirect()->route("cross_check.listCrossCheck", [
                'pre-load-pn' => $phap_nhan,
                'pre-load-year' => $nam
            ])->with('message',[
                'title' => 'Không thành công',
                'content' => 'Không tìm thấy bộ thanh toán lương',
                'type' => 'danger'
            ]);
        }

        $summaries = $this->summaryRepository->getDataBy([
            'order_id' => $order[0]['id'],
            'status' => 'all'
        ], false)->toArray();

        $sumSummaries = [];

        foreach ($summaries as $key => $value) {
            foreach ($value as $i => $field) {
                if (is_integer($field) && $i !== "order_id") {
                    $sumSummaries[0][$i] = !isset($sumSummaries[0][$i]) ? $field : $sumSummaries[0][$i] + $field;
                }

                if ($i == "order_id") {
                    $sumSummaries[0]["order_id"] = $field;
                }
            }
        }

        $sumSummaries[0]['noi_dung'] = $order[0]['noi_dung'];
        $sumSummaries[0]['serial'] = $order[0]['serial'];

        $sumSummaries = json_encode($sumSummaries, true);

        return view("cross_check.show-salary", compact('thang','info' , 'nam', 'order', 'sumSummaries', 'phap_nhan', 'isDone'));
    }

    public function showByMonth(Request $request, $luong, $phap_nhan, $thang, $nam) {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        if ($luong == "luong") {
            return $this->showByMonthSalary($request, $luong, $phap_nhan, $thang, $nam);
        }

        $val = intval($thang);
        $isDoneTCB = false;
        $isDoneAccounter = false;
        if ($val < 1 || $val > 12) {
            return "Wrong month";
        }

        $crossCheckInfo = $this->crossCheckInfoRepository->getDataBy([
            "phap_nhan" => $phap_nhan,
            "thang" => $thang,
            "nam" => $nam,
            "is_salary" => $luong == "luong" ? 1 : 0
        ]);

        if (!$crossCheckInfo->count()) {
            return redirect()->route("cross_check.importPanel", [
                "phap_nhan" => $phap_nhan,
                "thang" => $thang,
                "nam" => $nam,
                "is_salary" => $luong == "luong" ? 1 : 0
            ]);
        }
        $crossInfoId = $crossCheckInfo[0]->id;
        $data = $this->crossCheckRepository->getDataBy([
            'info_id' => $crossCheckInfo[0]->id,
            "temp_order" => true,
            "with_active" => true
        ], false)->toArray();

        foreach ($data as $key => $value) {
            if ($value['active'] == 0) {
                $value['order_id'] = null;
                $value['temp_order'] = 0;
                $this->crossCheckRepository->saveData($value);
                unset($data[$key]);
            }
        }

        if (!count($data)) {
            $isDoneTCB = true;
            if ($crossCheckInfo[0]->ke_toan_check) {
                $isDoneAccounter = true;
            }
        }

        $listSerial = array_map('strval', array_filter(array_column($data, "serial")));
        $listOrders = $this->orderRepository->getDataBy([
            "filters" => [
                [
                    "field" => "serial",
                    "operator" => "IN",
                    "value" => $listSerial
                ],
                [
                    "field" => "order_info.status",
                    "value" => 0
                ]
            ]
        ], false, true)->toArray();

        $crossChecked = [];
        foreach ($data as $key => $value) {
            $searched = search($listOrders, "serial", $value['serial'], true);

            if(!empty($searched)) {

                $ele = $value;
                $ele['order_id'] = $searched['id'];
                $ele['temp_order'] = false;

                if (intval($value['ps_no']) != intval($searched['quy_doi'])
                    || intval($value['thue']) != intval($searched['thue']) || $value['phap_nhan'] != $searched['phap_nhan']) {
                    $ele['temp_order'] = true;
                }

                $crossChecked[] = $ele;
            }
        }

        DB::beginTransaction();
        try {
            if (Topica::can("index.cross_check")) {
                foreach ($crossChecked as $key => $value) {
                    $this->crossCheckRepository->saveData($value);
                }
            }

            $sumAccReal = $this->crossCheckRepository->getDataBy([
                'selecRaw' => "sum(ps_no) as tong_thuc_tra, sum(thue) as tong_thue, sum(ps_no) + sum(thue) as tong_truoc_thue",
                "info_id" => $crossInfoId
            ], false)->toArray();

            $sumTcbReal = $this->summaryRepository->getDataBy([
                'select' => 'sum(summary.thuc_nhan) as tong_thuc_tra, sum(summary.thue_tam_trich) as tong_thue, sum(summary.tong_tnct) as tong_truoc_thue',
                'order_cross_check_by_info_id' =>  $crossInfoId,
                'status' => 'all'
            ], false)->toArray();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()->with("message", [
                "title" => "Không thành công",
                "content" => $e->getMessage(),
                "type" => "danger"
            ]);
        }

        return view('cross_check.show', compact('sumTcbReal', 'sumAccReal', 'thang', 'phap_nhan', 'nam', 'isDoneTCB', 'isDoneAccounter', 'crossInfoId', 'luong'));
    }

    public function getByMoth(Request $request, $luong, $phap_nhan, $thang, $nam) {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $requestParam = $request->all();

        if ($thang == 'null') {
            $thang = null;
        }

        $crossCheckInfo = $this->crossCheckInfoRepository->getDataBy([
            "phap_nhan" => $phap_nhan,
            "thang" => $thang,
            "nam" => $nam,
            "is_salary" => $luong == "luong" ? 1 : 0
        ], false);
//        d($crossCheckInfo);
        $queryData = [
            'info_id' => $crossCheckInfo[0]->id,
            "sort" => [
                [
                    "field" => "order_id",
                    "dir" => "DESC"
                ],
                [
                    "field" => "temp_order",
                    "dir" => "DESC"
                ]
            ],
            'with_order' => true
        ];

        if (isset($requestParam['sorters']) && $requestParam['sorters']) {
            $queryData['sort'] = $requestParam['sorters'];
        }

        if (isset($requestParam['temp_order']) && $requestParam['temp_order']) {
            $queryData['temp_order'] = $requestParam['temp_order'];
        }

        if (isset($requestParam['filter'])) {
            $queryData['filters'] = $requestParam['filter'];
        }
        if (isset($requestParam['filters'])) {
            $queryData['filters'] = $requestParam['filters'];
        }

        if (isset($requestParam['with_order'])) {
            $queryData['with_order'] = $requestParam['with_order'];
        }

        if (isset($requestParam['with_month_order'])) {
            $queryData['with_month_order'] = $requestParam['with_month_order'];
        }

        $queryData = array_merge($requestParam, $queryData);
        $defaultRepo = $this->crossCheckRepository;
        if ($thang == null) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        $data = $defaultRepo->getDataBy($queryData, isset($requestParam['pagination']) ? filter_var($requestParam['pagination'], FILTER_VALIDATE_BOOLEAN) : true)->toArray();
        if (isset($requestParam['with_month_order'])) {
            foreach ($data as $key => $ccY) {
                if (isset($ccY['order_id'])) {
                    unset($data[$key]);
                    continue;
                }
                if (isset($ccY['month_order']) && isset($ccY['month_tax'])){
                    if ($ccY['ps_no'] == $ccY['month_order']['quy_doi'] && $ccY['thue'] == $ccY['month_tax'][0]['sumTax']) {
                        unset($data[$key]);
                        continue;
                    }
                }
            }
        }
        if (isset($queryData['suggest']) && $queryData['suggest']) {
            $suggest = $queryData['suggest'];
            usort($data, function ($a, $b) use ($suggest) {
                $stra = json_decode($a['dien_giai']);
                $percentA = 0;
                foreach ($stra as $key => $value) {
                    similar_text($value, $suggest,$percentTemp);
                    $percentA = $percentTemp > $percentA ? $percentTemp : $percentA;
                }

                $strB = json_decode($b['dien_giai']);
                $percentB = 0;
                foreach ($strB as $key => $value) {
                    similar_text($value, $suggest,$percentTemp);
                    $percentB = $percentTemp > $percentB ? $percentTemp : $percentB;
                }

                return $percentB - $percentA;
            });
        }

//        $percentA = string_compare('1405180029_TOSSTD_ TT HĐ TKCM cho Nguyễn Thị Dương T4.2018', '1704180045_TOS_TT CP TKCM cho Phạm Thị Nghĩa T3.2018');
//        $percentB = string_compare('1704180045_TOS_TT CP TKCM cho Phạm Thị Nghĩa T3.2018 1704180045_VP01-1805004_TOS_TT CP TKCM cho Phạm Thị Nghĩa T3.2018', '1704180045_TOS_TT CP TKCM cho Phạm Thị Nghĩa T3.2018');
//        echo $percentA;
//        echo "  ";
//        echo $percentB;
//        exit();
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function pickOrders(Request $request) {
        Topica::canOrAbort("index.cross_check");
        $data = $request->all();
        $order_id = $data['order_id'];
        $cross_check_info_id = $data['cross_check_info_id'];
        $crossCheckInfo = $this->crossCheckInfoRepository->getDataBy([
            "id" => $cross_check_info_id
        ], false)->toArray();

        if (empty($crossCheckInfo)) {
            return "Wrong cross_check_info_id";
        }

        if (!isset($order_id)) {
            return redirect("/");
        }
        $crossCheckInfo = $crossCheckInfo[0];

        $cross_check_month = $crossCheckInfo['thang'];
        $cross_check_pn = $crossCheckInfo['phap_nhan'];
        $cross_check_year = $crossCheckInfo['nam'];
        $cross_check_luong = $crossCheckInfo['is_salary'] == 1 ? "luong" : "ngoai-luong";

        Topica::canCrossOrAbort("index.cross_check", $cross_check_pn);

        $order = $this->orderRepository->getDataBy([
            "order_info.id" => $order_id
        ], false, true);

        if (empty($order->toArray())) {
            return "Wrong order_id";
        }

        $order = $order[0];

        return view("cross_check.pick-orders", compact("order", "cross_check_info_id", "cross_check_month", "cross_check_year", "cross_check_pn", "cross_check_luong"));
    }

    public function mergeOrderYear(Request $request) {
        $data = $request->all();
        Topica::canCrossOrAbort("index.cross_check", $data['cross_check_pn']);
        header('Content-Type: application/json');
        DB::beginTransaction();
        try{
            foreach ($data['data'] as $key => $value) {
                if (isset($value['id'])) {
                    $this->crossCheckYearRepository->updateOrderId($value['id'], $data['order_id']);
                }
            }
            DB::commit();
            $result = [
                'result' => 'success',
                'redirect_url' => route('cross_check.showByYear', [
                    'phap_nhan' => $data['cross_check_pn'],
                    'nam' => $data['cross_check_year']
                ])
            ];
            echo json_encode($result);
        } catch (\Exception $e) {
            DB::rollback();
            echo json_encode($e->getMessage());
        }
    }

    public function mergeOrder(Request $request) {
        $data = $request->all();
        Topica::canCrossOrAbort("index.cross_check", $data['cross_check_pn']);
        if ($data['cross_check_month'] == "null" || $data['cross_check_month'] == null) {
            $this->mergeOrderYear($request);
            exit();
        }
        header('Content-Type: application/json');
        DB::beginTransaction();
        try{
            foreach ($data['data'] as $key => $value) {
                if (isset($value['id'])) {
                    $this->crossCheckRepository->updateOrderId($value['id'], $data['order_id']);
                }
            }
            DB::commit();
            $result = [
                'result' => 'success',
                'redirect_url' => route('cross_check.showByMonth', [
                    'phap_nhan' => $data['cross_check_pn'],
                    'thang' => $data['cross_check_month'],
                    'nam' => $data['cross_check_year'],
                    'luong' => $data['cross_check_luong'],
                ])
            ];
            echo json_encode($result);
        } catch (\Exception $e) {
            DB::rollback();
            echo json_encode($e->getMessage());
        }
    }

    public function removeOrderId(Request $request)
    {
        Topica::canOrAbort("index.cross_check");
        $data = $request->all();

        if(!isset($data['crossInfoId']) && !isset($data['orderId'])) {
            return null;
        }
        header('Content-Type: application/json');
        DB::beginTransaction();


        $defaultRepo = $this->crossCheckRepository;

        if (isset($data['cross_year']) && $data['cross_year']) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        try {
            $defaultRepo->updateOrderId(['order_id' => $data['orderId']], null);

            $delete = $this->orderRepository->delete([
                "id" => $data['orderId'],
                "noSerial" => (isset($data['cross_year']) && $data['cross_year']) ? null : true
            ]);

            $result = [
                'result' => 'success'
            ];
            echo json_encode($result);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $result = [
                'result' => 'fail',
                'content' => $e->getMessage()
            ];
            echo json_encode($result);
        }
    }

    public function listCrossCheck()
    {
        Topica::canOrAbort("index.cross_check_status");
        $listYear = range(2018, date("Y"));
        $listYear = array_combine($listYear, $listYear);
        return view("cross_check.list", compact( "listYear"));
    }

    public function getListCrossCheck(Request $request)
    {
        Topica::canOrAbort("index.cross_check_status");
        $data = $request->all();
        if (isset($data['nam']) && isset($data['phap_nhan'])) {

            Topica::canCrossOrAbort("index.cross_check_status", $data['phap_nhan']);
            $conditions['nam'] =  $data['nam'];
            $conditions['phap_nhan'] =  $data['phap_nhan'];
            $conditions['withCrossChecks'] = true;
            $crossCheckInfos = $this->crossCheckInfoRepository->getDataBy($conditions, false)->toArray();
//            var_dump($crossCheckInfos);exit();
            $currentMonth = $data['nam'] < date("Y") ? 12 : date("m");
//            var_dump($data['nam'] ,date("Y") );
//            exit();
            $months = range(1, $currentMonth);
            $response = [];
            $salaryOrders = $this->orderRepository->getDataBy([
                "filters" => [
                    [
                        "field" => "phap_nhan",
                        "value" => $data["phap_nhan"]
                    ],
                    [
                        "field" => "month",
                        "operator" => "in",
                        "value" => $months
                    ],
                    [
                        "field" => "isSalary",
                        "operator" => "=",
                        "value" => 1
                    ]
                ]
            ], false)->toArray();

//            "id" => 2
//            "phap_nhan" => "EDT"
//            "thang" => 7
//            "nam" => 2018
//            "ke_toan_check" => 1
//            "created_at" => "2018-08-17 03:33:47"
//            "updated_at" => "2018-08-22 11:07:52"
//            "is_salary" => 0
//            "ke_toan_id" => 1
//            "countCross" => 81
//            "countUnDone" => null

            foreach ($months as $key => $value) {
                $haveNoneSalary = findByConditions($crossCheckInfos, [
                    'is_salary' => 0,
                    'thang' => $value,
                    'nam' => $conditions['nam']
                ]);

                if ($haveNoneSalary) {
                    $response[] = $haveNoneSalary;
                } else {
                    $response[] = [
                        'thang' => $value,
                        'is_salary' => 0,
                        'nam' => $conditions['nam']
                    ];
                }


                $haveSalaryOrder = findByConditions($salaryOrders, [
                    'month' => "".$value,
                    'year' => "".$conditions['nam']
                ]);

                $haveSalaryCross = findByConditions($crossCheckInfos, [
                    'is_salary' => 1,
                    'thang' => $value,
                    'nam' => $conditions['nam']
                ]);

                if ($haveSalaryOrder) {
                    $temp = [
                        'thang' => $haveSalaryOrder['month'],
                        'is_salary' => 1,
                        'nam' => $haveSalaryOrder['year'],
                        'countCross' => 1,
                        'countUnDone' => null
                    ];

                    if ($haveSalaryCross && $haveSalaryCross['ke_toan_check'] == 1) {
                        $temp['ke_toan_check'] = 1;
                    }

                    if ($haveNoneSalary) {
                        $temp['id'] = -1;
                    }

                    $response[] = $temp;
                } else {
                    $response[] = [
                        'thang' => $value,
                        'is_salary' => 1,
                        'nam' => $conditions['nam'],
//                        'countCross' => 1,
//                        'countUnDone' => null,
//                        'id' => -1
                    ];
                }
//                var_dump($response);
            }

            return response()->json($response);
        }

        return null;
    }

    public function removeCrossCheck(Request $request, $id)
    {
        $info = $this->crossCheckInfoRepository->getDataBy([
            "id" => $id
        ])->first()->toArray();

        if (empty($info)) {
            return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                "title" => "Thất bại",
                "content" => "Tắt đối soát thất bại: Bộ đối soát không tồn tại",
                "type" => "danger"
            ]);
        }
        Topica::canCrossOrAbort("remove.cross_check", $info['phap_nhan']);
        $data = $request->all();

        $crossYear = false;

        if (isset($data['crossYear']) && $data['crossYear']) {
            $crossYear = true;
        }

        $defaultRepo = $this->crossCheckRepository;
        if ($crossYear) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        try {
            DB::beginTransaction();
            $this->crossCheckInfoRepository->saveData([
                "id" => $id,
                "ke_toan_check" => 0
            ]);

            $crossChecks = $defaultRepo->getDataBy([
                "info_id" => $id
            ], false)->toArray();

            $ngayThanhToan = [];
            foreach ($crossChecks as $key => $value) {
                if ($value['order_id'] != null) {
                    $ngayThanhToan[$value['order_id']] = $value['ngay_chung_tu'];
                }
            }

            if ($info['is_salary'] == 1) {
                $data = $this->orderRepository->getDataBy([
                    "phap_nhan" => $info['phap_nhan'],
                    "isSalary" => 1,
                    "month" => $info['thang'],
                    "year" => $info['nam'],
                ])->first();
                $ngayThanhToan[$data->id] = $data;
            }

            foreach ($ngayThanhToan as $key => $value) {
                $this->orderRepository->updateStatus($key, [
                    "status" => OrderInfo::CROSS_CHECK_UNDONE,
                    "ngay_thanh_toan" => null
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();
            if ($crossYear) {
                return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                    "title" => "Thất bại",
                    "content" => "Tắt đối soát thất bại: ".$e->getMessage(),
                    "type" => "danger"
                ]);
            }
            return redirect()->route("cross_check.listCrossCheck")->with("message", [
                "title" => "Thất bại",
                "content" => "Tắt đối soát thất bại: ".$e->getMessage(),
                "type" => "danger"
            ]);
        }

        if ($crossYear) {
            return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                "title" => "Thành công",
                "content" => "Tắt đối soát thành công",
                "type" => "success"
            ]);
        }

        return redirect()->route("cross_check.listCrossCheck")->with("message", [
            "title" => "Thành công",
            "content" => "Hủy bỏ đối soát thành công",
            "type" => "success"
        ]);
    }

    public function cancelCrossCheck(Request $request, $id)
    {
        $info = $this->crossCheckInfoRepository->getDataBy([
            "id" => $id
        ])->first()->toArray();

        if (empty($info)) {
            return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                "title" => "Thất bại",
                "content" => "Hủy bỏ đối soát thất bại: Bộ đối soát không tồn tại",
                "type" => "danger"
            ]);
        }
        Topica::canCrossOrAbort("delete.cross_check", $info['phap_nhan']);
        $data = $request->all();

        $crossYear = false;

        if (isset($data['crossYear']) && $data['crossYear']) {
            $crossYear = true;
        }

        $defaultRepo = $this->crossCheckRepository;
        if ($crossYear) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        try {
            DB::beginTransaction();
            $this->crossCheckInfoRepository->saveData([
                "id" => $id,
                "ke_toan_check" => 0
            ]);

            //Nếu đối soát năm thì xóa hết các bộ thanh toán bổ sung
            if ($crossYear) {
                $crossNoneSerial = $defaultRepo->getDataBy([
                    "info_id" => $id
                ], false)->toArray();

                $orderNoneSerial = array_filter(array_unique(array_column($crossNoneSerial, "order_id")));
            } else {
                //Nếu đối soát tháng thì xóa các bộ thanh toán khoản có serial = null và cập nhật trạng thái của các bộ thanh toán của
                //khoản có serial
                $crossNoneSerial = $defaultRepo->getDataBy([
                    "serial" => "null",
                    "info_id" => $id
                ], false)->toArray();

                $crossChecks = $defaultRepo->getDataBy([
                    "info_id" => $id
                ], false)->toArray();

                $ngayThanhToan = [];
                foreach ($crossChecks as $key => $value) {
                    if ($value['order_id'] != null) {
                        $ngayThanhToan[$value['order_id']] = $value['ngay_chung_tu'];
                    }
                }

                if ($info['is_salary'] == 1) {
                    $data = $this->orderRepository->getDataBy([
                        "phap_nhan" => $info['phap_nhan'],
                        "isSalary" => 1,
                        "month" => $info['thang'],
                        "year" => $info['nam'],
                    ])->first();
                    $ngayThanhToan[$data->id] = $data;
                }

                foreach ($ngayThanhToan as $key => $value) {
                    $this->orderRepository->updateStatus($key, [
                        "status" => OrderInfo::CROSS_CHECK_UNDONE,
                        "ngay_thanh_toan" => null
                    ]);
                }

                $orderNoneSerial = array_filter(array_unique(array_column($crossNoneSerial, "order_id")));
            }

            $delete = $this->orderRepository->delete([
                "id" => $orderNoneSerial
            ], true);

            $defaultRepo->delete([
                "info_id" => $id
            ]);

            $this->crossCheckInfoRepository->delete([
                "id" => $id
            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();
            if ($crossYear) {
                return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                    "title" => "Thất bại",
                    "content" => "Hủy bỏ đối soát thất bại: ".$e->getMessage(),
                    "type" => "danger"
                ]);
            }
            return redirect()->route("cross_check.listCrossCheck")->with("message", [
                "title" => "Thất bại",
                "content" => "Hủy bỏ đối soát thất bại: ".$e->getMessage(),
                "type" => "danger"
            ]);
        }

        if ($crossYear) {
            return redirect()->route("cross_check.listCrossCheckYear")->with("message", [
                "title" => "Thành công",
                "content" => "Hủy bỏ đối soát thành công",
                "type" => "success"
            ]);
        }

        return redirect()->route("cross_check.listCrossCheck")->with("message", [
            "title" => "Thành công",
            "content" => "Hủy bỏ đối soát thành công",
            "type" => "success"
        ]);
    }

    public function doneAccounterYear(Request $request, $cross_id) {
        Topica::canOrAbort("export.cross_check_kt_check");
        $cci = $this->crossCheckYearRepository->getDataBy([
            "info_id" => $cross_id,
            "with_order" => true,
            "with_month_order" => true
        ], false)->toArray();

        //Tính chênh lệch giữa khoản năm và tháng
        foreach ($cci as $key => $value) {
            if (isset($value['order_id'])) {
                unset($cci[$key]);
                continue;
            }
            if (isset($value['month_order']) && $value['month_order']['quy_doi'] == $value['ps_no']
                && isset($value['month_tax']) && $value['month_tax'][0]['sumTax'] == $value['thue']) {
                unset($cci[$key]);
                continue;
            }
        }

        if (empty($cci)) {
            $message = $this->doneAccounter($request, $cross_id, false, true);
            return Redirect::back()->with("message", $message);
        } else {
            $message = [
                "title" => "Không thành công",
                "content" => "Bộ đối soát chưa được TCB hoàn thành",
                "type" => "danger"
            ];
            return Redirect::back()->with("message", $message);
        }
    }

    public function doneAccounter(Request $request, $cross_id, $noRender = false, $checkYear = false)
    {
        $conditions = [
            "id" => $cross_id,
            "withCrossChecks" => true,
            "first" => true
        ];
        $defaultRepo = $this->crossCheckRepository;
        if ($checkYear) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        $crossCheckInfo = $this->crossCheckInfoRepository->getDataBy($conditions, false);
        if ($crossCheckInfo !== null) {
            $crossCheckInfo = $crossCheckInfo->toArray();
            Topica::canCrossOrAbort("export.cross_check_kt_check", $crossCheckInfo['phap_nhan']);
            if (($crossCheckInfo['countCross'] != null && $crossCheckInfo['countUnDone'] == null) || $crossCheckInfo['is_salary'] == 1 || $checkYear == true) {
                try {
                    DB::beginTransaction();
                    $this->crossCheckInfoRepository->saveData([
                        "id" => $cross_id,
                        "ke_toan_check" => 1
                    ]);

                    if ($noRender) {
                        $requestData = $request->all();
                        $data = [
                            'month' => $requestData['thang'],
                            'year' => $requestData['nam'],
                            'phap_nhan' => $requestData['phap_nhan'],
                            'isSalary' => $requestData['is_salary'],
                        ];
                        $orders = $this->orderRepository->getDataBy($data, false)->toArray();

                        foreach ($orders as $key => $value) {
                            $this->orderRepository->updateStatus($value['id'], [
                                "status" => OrderInfo::CROSS_CHECK_DONE,
                                "ngay_thanh_toan" => \DateTime::createFromFormat('d/m/Y', $requestData['thanh-toan'])->format("Y-m-d H:i:s"),
                                "dot_thanh_toan" => json_encode($requestData['dates'])
                            ]);
                        }
                    } else {
                        $crossChecks = $defaultRepo->getDataBy([
                            "info_id" => $cross_id
                        ], false)->toArray();

                        $ngayThanhToan = [];
                        foreach ($crossChecks as $key => $value) {
                            if ($value['order_id'] != null) {
                                $ngayThanhToan[$value['order_id']] = $value['ngay_chung_tu'];
                            }
                        }

                        foreach ($ngayThanhToan as $key => $value) {
                            $this->orderRepository->updateStatus($key, [
                                "status" => OrderInfo::CROSS_CHECK_DONE,
                                "ngay_thanh_toan" => $value
                            ]);
                        }
                    }

                    DB::commit();
                    $message = [
                        "title" => "Thành công",
                        "content" => "Hoàn thành bộ đối soát thành công",
                        "type" => "success"
                    ];
                    if ($noRender || $checkYear) {
                        return $message;
                    }

                    return redirect()->route("cross_check.listCrossCheck", [
                        'pre-load-pn' => $crossCheckInfo['phap_nhan'],
                        'pre-load-year' => $crossCheckInfo['nam'],
                    ])->with("message", $message);

                } catch (\Exception $e) {
                    DB::rollback();
                    $message = [
                        "title" => "Thất bại",
                        "content" => "Hoàn thành đối soát thất bại: ".$e->getMessage(),
                        "type" => "danger"
                    ];
                    if ($noRender) {
                        return $message;
                    }
                    return redirect()->route("cross_check.listCrossCheck")->with("message", $message);
                }
            } else {
                $message = [
                    "title" => "Không thành công",
                    "content" => "Bộ đối soát chưa được TCB hoàn thành",
                    "type" => "danger"
                ];
                if ($noRender) {
                    return $message;
                }
                return Redirect::back()->with("message", $message);
            }
        } else {
            $message = [
                "title" => "Lỗi",
                "content" => "Bộ đối soát không tồn tại",
                "type" => "danger"
            ];

            if ($noRender) {
                return $message;
            }
            return Redirect::back()->with("message", $message);
        }
    }

    public function setActive(Request $request, $cross_id)
    {
        $requestData = $request->all();
        if (!isset($requestData['active'])) {
            return response()->json([
                'message' => 'Trường active cần được xác định'
            ]);
        }

        if ($requestData['active'] == 0 && (!isset($requestData['reason']) || trim($requestData['reason']) == "")) {
            return response()->json([
                'message' => 'Lý do bỏ qua phải được nhập'
            ]);
        }

        $defaultRepo = $this->crossCheckRepository;

        if (isset($requestData['cross_year']) && $requestData['cross_year']) {
            $defaultRepo = $this->crossCheckYearRepository;
        }
        $cross = $defaultRepo->getDataBy([
            "id" => $cross_id,
            "with_active" => true
        ])->first()->toArray();

        if (empty($cross)) {
            return response()->json([
                'message' => 'Khoản đối soát không tồn tại'
            ]);
        }
        Topica::canCrossOrAbort("index.cross_check", $cross['phap_nhan']);

        $active = $requestData['active'];
        $reason = $active == 0 ? $requestData['reason'] : null;
        $data = [
            'id' => $cross_id,
            'active' => $active,
            'reason' => $reason,
            'order_id' => null
        ];
        return $defaultRepo->saveData($data);
    }

    public function export(Request $request, $phap_nhan, $thang, $nam) {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $requestParam = $request->all();
        $crossCheckInfo = $this->crossCheckInfoRepository->getDataBy([
            "phap_nhan" => $phap_nhan,
            "thang" => $thang,
            "nam" => $nam,
            "is_salary" => 0
        ], false);
//        d($crossCheckInfo);
        $queryData = [
            'info_id' => $crossCheckInfo[0]->id,
            'pagination' => false,
            'with_order' => true,
            'with_active' => true,
            'sort' => [
                [
                    'field' => 'order_id',
                    'dir' => 'asc'
                ]
            ]
        ];

        $queryData = array_merge($requestParam, $queryData);
        $data = $this->crossCheckRepository->getDataBy($queryData, false)->toArray();

        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleTcb = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleAccounter = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleCompare = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

        $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "ĐỐI SOÁT THUẾ THU NHẬP CÁ NHÂN")->getStyle( 'A1' )->applyFromArray($style)->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Pháp nhân")->getStyle( 'A2' )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', strtoupper($phap_nhan));
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Tháng/năm")->getStyle( 'A3' )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', $thang.'/'.$nam);

        $objPHPExcel->getActiveSheet()->mergeCells('G4:I4');
        $objPHPExcel->getActiveSheet()->mergeCells('J4:L4');
        $objPHPExcel->getActiveSheet()->mergeCells('M4:O4');

        //header
        $objPHPExcel->getActiveSheet()->getStyle( 'A4:P5' )->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->getStyle( 'G5:I5' )->applyFromArray($styleAccounter);
        $objPHPExcel->getActiveSheet()->getStyle( 'J5:L5' )->applyFromArray($styleTcb);
        $objPHPExcel->getActiveSheet()->getStyle( 'M5:O5' )->applyFromArray($styleCompare);

        $objPHPExcel->getActiveSheet()->SetCellValue('A5', "STT");
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', "Serial");
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', "Ngày chứng từ");
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', "Mã chứng từ");
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', "Số chứng từ");

        $objPHPExcel->getActiveSheet()->SetCellValue('E5', "Diễn giải");


        $objPHPExcel->getActiveSheet()->SetCellValue('F5', "Tài khoản đối ứng");

        $objPHPExcel->getActiveSheet()->SetCellValue('G4', "Kế toán")->getStyle( 'G4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('G5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('J4', "TCB")->getStyle( 'J4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('J5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('K5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('L5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('M4', "Chênh lệch")->getStyle( 'M4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('M5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('N5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('O5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('P5', "Bộ thanh toán");

        $startRender = 6;
        $lastRow = 6;

        foreach ($data as $key => $value) {
            $renderRow = $key + $startRender;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$renderRow, $key + 1);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$renderRow, strval($value['serial']),\PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$renderRow, date("d/m/Y", strtotime($value['ngay_chung_tu'])));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$renderRow, $value['ma_chung_tu']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$renderRow, $value['so_chung_tu']);

            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$renderRow, implode("\n", json_decode($value['dien_giai'])));

            $objPHPExcel->getActiveSheet()->getStyle('E'.$renderRow)->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$renderRow, $value['tai_khoan_doi_ung']);

            //Kế toán
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$renderRow, $value['ps_no'] + $value['thue']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$renderRow, $value['thue']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$renderRow, $value['ps_no']);

            $noOrder = $value['order_id'] == null || $value['active'] == 0;
            //TCB
            if ($value['order_id'] != null && $value['temp_order'] == 0) {
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$renderRow, $value['ps_no'] + $value['thue']);
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$renderRow, $value['thue']);
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$renderRow, $value['ps_no']);
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$renderRow, $noOrder ? "" : $value['order']['quy_doi'] + $value['tax'][0]['sumTax']);
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$renderRow, $noOrder ? "" : $value['tax'][0]['sumTax']);
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$renderRow, $noOrder ? "" : $value['order']['quy_doi']);
            }
            //Chênh lệch
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$renderRow, $noOrder ? "" : "=(G{$renderRow}-J{$renderRow})");
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$renderRow, $noOrder ? "" : "=(H{$renderRow}-K{$renderRow})");
            $objPHPExcel->getActiveSheet()->SetCellValue('O'.$renderRow, $noOrder ? "" : "=(I{$renderRow}-L{$renderRow})");

            if ($value['active'] == 0) {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, "Bỏ qua do:\n{$value['reason']}");
                $objPHPExcel->getActiveSheet()->getStyle('P'.$renderRow)->getAlignment()->setWrapText(true);
            } else if ($value['order_id'] == null) {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, "Không thấy");
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, $noOrder ? "" : "F-{$value['order']['id']} ({$value['serial']})");
            }


            $lastRow = $renderRow;
        }

        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$startRender.':'.'P'.$lastRow )->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle( 'G'.$startRender.':'.'O'.$lastRow )->getNumberFormat()->setFormatCode('###,##0');
        $objPHPExcel->getActiveSheet()->getStyle( 'G'.($lastRow + 1).':'.'O'.($lastRow + 1) )->getNumberFormat()->setFormatCode('###,##0');

        $objPHPExcel->getActiveSheet()->SetCellValue('E'.($lastRow + 1), "Tổng")->getStyle( 'E'.($lastRow + 1) )->getFont()->setBold( true );;

        $objPHPExcel->getActiveSheet()->SetCellValue('G'.($lastRow + 1), "=SUM(G{$startRender}:G{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.($lastRow + 1), "=SUM(H{$startRender}:H{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.($lastRow + 1), "=SUM(I{$startRender}:I{$lastRow})");

        $objPHPExcel->getActiveSheet()->SetCellValue('J'.($lastRow + 1), "=SUM(J{$startRender}:J{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.($lastRow + 1), "=SUM(K{$startRender}:K{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.($lastRow + 1), "=SUM(L{$startRender}:L{$lastRow})");

        $objPHPExcel->getActiveSheet()->SetCellValue('M'.($lastRow + 1), "=SUM(M{$startRender}:M{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.($lastRow + 1), "=SUM(N{$startRender}:N{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.($lastRow + 1), "=SUM(O{$startRender}:O{$lastRow})");

        foreach(range('A','P') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->SetCellValue('C'.($lastRow + 5), "Kế toán tổng hợp")->getStyle( 'C'.($lastRow + 5) )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.($lastRow + 5), "TCB")->getStyle( 'N'.($lastRow + 5) )->getFont()->setBold( true );

        $textLuong = "ngoài lương";
        $textThang = sprintf("%02d", $thang);
        $fileName = "Đối soát {$textLuong}-{$textThang}{$nam}-{$phap_nhan}.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
//        return response()->json($data);
    }

    public function exportSalary(Request $request, $phap_nhan, $thang, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $objPHPExcel = \PHPExcel_IOFactory::load("./assets/templateExcel/DoiSoatLuong.xlsx");

        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $order = $this->orderRepository->getDataBy([
            'month' => $thang,
            'year' => $nam,
            'phap_nhan' => $phap_nhan,
            'isSalary' => 1
        ], false)->toArray();

        $conditions = [
            'thang' => $thang,
            'nam' => $nam,
            'phap_nhan' => $phap_nhan,
            'is_salary' => 1
        ];

        $info = $this->crossCheckInfoRepository->getDataBy($conditions, false)->toArray();

        if (empty($info)) {
            $this->crossCheckInfoRepository->saveData($conditions);
        }

        if (empty($order)) {
            return redirect()->route("cross_check.listCrossCheck", [
                'pre-load-pn' => $phap_nhan,
                'pre-load-year' => $nam
            ])->with('message',[
                'title' => 'Không thành công',
                'content' => 'Không tìm thấy bộ thanh toán lương',
                'type' => 'danger'
            ]);
        }

        $summaries = $this->summaryRepository->getDataBy([
            'order_id' => $order[0]['id'],
            'status' => 'all'
        ], false)->toArray();

        $sumSummaries = [];

        foreach ($summaries as $key => $value) {
            foreach ($value as $i => $field) {
                if (is_integer($field) && $i !== "order_id") {
                    $sumSummaries[0][$i] = !isset($sumSummaries[0][$i]) ? $field : $sumSummaries[0][$i] + $field;
                }

                if ($i == "order_id") {
                    $sumSummaries[0]["order_id"] = $field;
                }
            }
        }

        $sumSummaries[0]['noi_dung'] = $order[0]['noi_dung'];
        $sumSummaries[0]['serial'] = $order[0]['serial'];

        $startCells = findExcelCellByValue($objPHPExcel, "start_print");

        $phap_nhan_cell = findExcelCellByValue($objPHPExcel, "phap_nhan");
        $thang_cell = findExcelCellByValue($objPHPExcel, "thang_doi_soat");


        $objPHPExcel->getActiveSheet()->SetCellValue($phap_nhan_cell[0], $phap_nhan);
        $objPHPExcel->getActiveSheet()->SetCellValue($thang_cell[0], $thang."/".$nam);

        if (empty($startCells)) {
            return redirect()->route("cross_check.listCrossCheck", [
                'pre-load-pn' => $phap_nhan,
                'pre-load-year' => $nam
            ])->with('message',[
                'title' => 'Không thành công',
                'content' => 'Định dạng file không đúng(Không tìm thấy trường start_print)',
                'type' => 'danger'
            ]);
        }

        $data = $sumSummaries[0];

        $startCell = $startCells[0];

        $titleValue = [
            "STT" => "1",
            "Nội dung" => $data['noi_dung'],
            "Tổng TN trước thuế" => $data['sum_thu_nhap_truoc_thue'],
            "Thu nhập không chịu thuế" => $data["sum_non_tax"],
            "Tổng thu nhập chịu thuế" => $data['sum_thu_nhap_truoc_thue'] - $data["sum_non_tax"],
            "Bảo hiểm xã hội nhân viên đóng" => $data["sum_bhxh"],
            "Tổng thuế TNCN" => $data["sum_thue_tam_trich"],
            "Thuế TNCN đã trích" => $data["thue_da_trich"],
            "Thuế TNCN bổ sung" => $data["sum_thue_tam_trich"] - $data["thue_da_trich"],
            "Tổng Thực nhận" => $data["sum_thuc_nhan"],
            "Đã thanh toán (các khoản ngoài lương)" => $data["da_thanh_toan"],
            "Còn lại" => $data["con_lai_can_thanh_toan"],
            "Bộ thanh toán" => "F-{$data["order_id"]}",
            "Ngày thanh toán" => $order[0]['dot_thanh_toan'] == null ? "" : implode(",\n", json_decode($order[0]['dot_thanh_toan'])),
        ];
        $startRow = intval(preg_replace("/[a-zA-Z]+/", "", $startCell));
//        ddie($startCell);

        foreach (range("A", "N") as $key => $value) {
//            echo $value.$startRow;
            $cell = $objPHPExcel->getActiveSheet()->getCell($value.($startRow - 1));
            if ($cell->isInMergeRange()) {
                $firstMerge = explode(":", $cell->getMergeRange())[0];
                $val = $objPHPExcel->getActiveSheet()->getCell($firstMerge)->getValue();
            } else {
                $val = $objPHPExcel->getActiveSheet()->getCell($value.($startRow - 1))->getValue();
            }
            $objPHPExcel->getActiveSheet()->SetCellValue($value.$startRow, $titleValue[$val]);
        }
        $objPHPExcel->getActiveSheet()->getStyle( 'A6:N6' )->applyFromArray($styleBorder);
        $objPHPExcel->getActiveSheet()->getStyle( 'A6:N6' )->getNumberFormat()->setFormatCode('###,##0');
        $objPHPExcel->getActiveSheet()->getStyle('N6')->getAlignment()->setWrapText(true);


        $objPHPExcel->getActiveSheet()->SetCellValue("B".($startRow + 4), "Kế toán tổng hợp");

        $objPHPExcel->getActiveSheet()->SetCellValue("M".($startRow + 4), "TCB");

//        ddie($sumSummaries);
        $textLuong = "lương";
        $textThang = sprintf("%02d", $thang);
        $fileName = "Đối soát {$textLuong}-{$textThang}{$nam}-{$phap_nhan}.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    function listCrossCheckYear(Request $request)
    {
        Topica::canOrAbort("index.cross_check_status");
        return view("cross_check.list-year");
    }

    function getListCrossCheckYear(Request $request)
    {
        Topica::canOrAbort("index.cross_check_status");
        $requestData = $request->all();
        $phap_nhan = isset($requestData['phap_nhan']) ? $requestData['phap_nhan'] : null;

        $response = [];

        $ccYear = $this->crossCheckInfoRepository->getDataBy([
            "crossYearByPN" => $phap_nhan
        ], false)->toArray();

        $years = range(2018, date("Y"));
        foreach ($years as $key => $value) {
            $found = findByConditions($ccYear, [
                'nam' => $value
            ]);

            if ($found) {
                $response[] = $found;
            } else {
                $response[] = [
                    "nam" => $value,
                    "id" => -1
                ];
            }
        }

        return response()->json($response);
    }

    function crossCheckYear(Request $request, $phap_nhan, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $ccYear = $this->crossCheckInfoRepository->getDataBy([
            "thang" => null,
            "nam" => $nam,
            "phap_nhan" => $phap_nhan,
            "withCrossChecks" => true
        ], false)->first();

        $ccInfosMonth = $this->crossCheckInfoRepository->getDataBy([
            "ke_toan_check" => 1,
            "thang" => "notNull",
            "nam" => $nam,
            "phap_nhan" => $phap_nhan
        ], false)->toArray();

        $isDone = true;

        foreach (range("1", date("m")) as $key => $value) {
            $founds = findByConditions($ccInfosMonth, ["thang" => $value], false, true);
            if (!$founds || count($founds) != 2) {
                $isDone = false;
                break;
            } else {
                foreach ($founds as $k => $v) {
                    if ($v['ke_toan_check'] != 1) {
                        $isDone = false;
                        break;
                    }
                }
            }
            if (!$isDone) break;
        }

        if (!$isDone) {
            return view("cross_check.emptyYear", compact("phap_nhan", "nam"));
        }

        if (empty($ccYear)) {
            return view("cross_check.import-year", compact("phap_nhan", "nam"));
        }

        $ccYear = $ccYear->toArray();

        $count = $this->crossCheckYearRepository->getDataBy([
            "info_id" => $ccYear['id']
        ], false)->count();
        if ($count == 0) {
            return view("cross_check.import-year", compact("phap_nhan", "nam"));
        } else {
            return \redirect()->route("cross_check.getByYear", [
                "phap_nhan" => $phap_nhan,
                "nam" => $nam
            ]);
        }
    }

    function importHandleYear(Request $request, $phap_nhan, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check", $phap_nhan);
        if (!$request->hasFile('importFile'))
        {
            return "File not found";
        }

        $file = $request->file('importFile');

        try {
            $data = $this->importExcel->getDataCrossCheck($file);
            if (empty($data)) {
                throw new \Exception("empty data");
            }
        } catch (\Exception $e) {
            throw $e;
            return Redirect::back()->with("message", [
                'title' => 'Lỗi',
                'content' => 'File sai định dạng hoặc không có dữ liệu',
                'type' => 'danger'
            ]);
        }

//        return response()->json($data);

        $mergeData = $this->proccessExcelData($data);

        if (!is_array($mergeData)) {
            return $mergeData;
        }

//        return response()->json($mergeData);

        DB::beginTransaction();
        try {
            $crossCheckData = [
                "phap_nhan" => $phap_nhan,
                "thang" => null,
                "nam" => $nam,
                "is_salary" => 0
            ];
            $crossCheckInfo = $this->crossCheckInfoRepository->saveData($crossCheckData);

            $this->crossCheckYearRepository->delete([
                'info_id' => $crossCheckInfo->id
            ]);
            foreach ($mergeData as $key => &$value) {
                $value['dien_giai'] = json_encode($value['dien_giai'], JSON_UNESCAPED_UNICODE);
                $value['info_id'] = $crossCheckInfo->id;
                $value['phap_nhan'] = $crossCheckInfo->phap_nhan;
                if ($value['ps_co'] === null) {
                    $value['ps_co'] = 0;
                }
                if ($value['ma_chung_tu_0'] === null) {
                    $value['ma_chung_tu_0'] = 0;
                }
            }

            $this->crossCheckYearRepository->saveData($mergeData, true);
            DB::commit();

            return \redirect()->route("cross_check.getByYear", [
                "phap_nhan" => $phap_nhan,
                "nam" => $nam
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

        if (empty($cciYear)) {
            return view("cross_check.import-year", compact("phap_nhan", "nam"));
        }
    }

    function getByYear(Request $request, $phap_nhan, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $cci = $ccYear = $this->crossCheckInfoRepository->getDataBy([
            "thang" => null,
            "nam" => $nam,
            "phap_nhan" => $phap_nhan
        ], false)->first()->toArray();

        $ccYear = $this->crossCheckRepository->getDataBy([
            "nam" => $nam,
            "phap_nhan" => $phap_nhan,
            "with_order" => true,
            "with_active" => true
        ], false)->toArray();

        $mergeData = $this->crossCheckYearRepository->getDataBy([
            "info_id" => $cci["id"],
            "with_order" => true,
            "with_active" => true
        ], false)->toArray();

        foreach ($mergeData as $key => $value) {
            $mergeData[$value['serial'] == null ? "no_".$key : $value['serial']] = $mergeData[$key];
            unset($mergeData[$key]);
        }

        $result = [];

        $added = [];



        DB::beginTransaction();
        //Thực hiện đối soát theo năm
        try {
            foreach ($ccYear as $key => $value) {
                $temp = [
                    "serial" => $value['serial'] == null ? "no-".$key : $value['serial'],
                    "dien_giai" => json_decode($value['dien_giai']),
                    "ma_khach" => $value['ma_khach'],
                    "cross_id" => $value['id'],
                    "order_id" => $value['order_id'],
                    "ma_chung_tu" => $value['ma_chung_tu'],
                    "order" => $value['order'],
                    "tax" => $value['tax'],
                    "additional_order" => null,
                    "additional_tax" => null,
                    "active" => $value['active'],
                    "cross_year_id" => null,
                    "nam" => [
                        "thu_nhap" => 0,
                        "thue" => 0,
                        "thuc_nhan" => 0,
                    ],
                    "thang" => [
                        "thu_nhap" => $value['ps_no'] + $value['thue'],
                        "thue" => $value['thue'],
                        "thuc_nhan" => $value['ps_no'],
                    ]
                ];

                $temp = array_merge($temp, $value);

                $update = [];
                //Trường hợp khoản đối soát tháng có serial = null thì thực hiện tìm kiếm khoản theo diễn giải
                if ($value['serial'] == null) {
                    $found = findByConditions($mergeData, [
                        "dien_giai" => $value['dien_giai']
                    ], true);
                    //Nếu tìm được theo diễn giải thì khởi tạo mảng tìm được
                    if ($found) {
                        $temp['nam']['thu_nhap'] = $mergeData[$found]['ps_no'] + $mergeData[$found]['thue'];
                        $temp['nam']['thue'] = $mergeData[$found]['thue'];
                        $temp['nam']['thuc_nhan'] = $mergeData[$found]['ps_no'];
                        $temp['cross_year_id'] = $mergeData[$found]['id'];
                        $temp['additional_order'] = $mergeData[$found]['order'];
                        $temp['dien_giai'] = $mergeData[$found]['dien_giai'];
                        $temp['additional_tax'] = $mergeData[$found]['tax'];
                        $temp['active'] = $mergeData[$found]['active'];
                        $temp['reason'] = $mergeData[$found]['reason'];

                        //Lưu dữ liệu cần thiết từ tháng sang năm
                        $saveForYear = [];
                        if ($value['active'] == 0 && $mergeData[$found]['active'] != 0) {
                            $saveData = [
                                "id" => $mergeData[$found]['id'],
                                "active" => $value['active'],
                                "reason" => $value['reason'],
                            ];
                            $saveForYear = array_merge($saveForYear, $saveData);
                        }
                        if ($value['order_id'] !== null && $mergeData[$found]['month_order_id'] != $value['order_id']) {
                            $saveData = [
                                "id" => $mergeData[$found]['id'],
                                "month_order_id" => $value['order_id'],
                            ];
                            $saveForYear = array_merge($saveForYear, $saveData);
                        }
                        if (!empty($saveForYear)) {
                            $this->crossCheckYearRepository->saveData($saveData);
                        }

                        $mergeData[$temp['serial']] = $mergeData[$found];
                        unset($mergeData[$found]);
                    } else {
                        //Nếu không tìm thấy thì thực hiện lưu mới khoản đối soát năm
                        $saveData = [
                            "serial" => $value['serial'],
                            "ngay_chung_tu" => $value['ngay_chung_tu'],
                            "ma_chung_tu_0" => $value['ma_chung_tu_0'] == null ?? "",
                            "ma_chung_tu" => $value['ma_chung_tu'],
                            "so_chung_tu" => $value['so_chung_tu'],
                            "ma_khach" => $value['ma_khach'],
                            "ten_khach" => $value['ten_khach'],
                            "dien_giai" => $value['dien_giai'],
                            "tai_khoan" => $value['tai_khoan'],
                            "tai_khoan_doi_ung" => $value['tai_khoan_doi_ung'],
                            "ps_no" => 0,
                            "ps_co" => 0,
                            "thue" => 0,
                            "ma_du_an" => $value['ma_du_an'],
                            "phap_nhan" => $phap_nhan,
                            "status" => CrossCheckYear::NONE_EXIST_IN_YEAR,
                            "info_id" => $cci['id'],
                            "phap_nhan" => $value['phap_nhan'],
                            "month_order_id" => $value['order_id'],
                            "active" => $value['active'],
                            "reason" => $value['reason'],
                        ];

                        $savedCC = $this->crossCheckYearRepository->saveData($saveData);
                        $temp['cross_year_id'] = $savedCC->id;
                        $temp['active'] = $savedCC->active;
                        $temp['reason'] = $savedCC->reason;
                    }
                    if ($value['order_id'] == null || $value['temp_order'] == 1) {
                        $temp['thang']['thu_nhap'] = 0;
                        $temp['thang']['thue'] = 0;
                        $temp['thang']['thuc_nhan'] = 0;
                    }
                    $diff[] = $temp;
                } else if (!isset($mergeData[$value['serial']])) {
                    //Nếu không tìm thấy khoản đối soát năm theo serial của khoản tháng
                    //Thì thực hiện tạo khoản đối soát năm mới giống như khoản tháng
                    $saveData = [
                        "serial" => $value['serial'],
                        "ngay_chung_tu" => $value['ngay_chung_tu'],
                        "ma_chung_tu_0" => $value['ma_chung_tu_0'] == null ?? "",
                        "ma_chung_tu" => $value['ma_chung_tu'],
                        "so_chung_tu" => $value['so_chung_tu'],
                        "ma_khach" => $value['ma_khach'],
                        "ten_khach" => $value['ten_khach'],
                        "dien_giai" => $value['dien_giai'],
                        "tai_khoan" => $value['tai_khoan'],
                        "tai_khoan_doi_ung" => $value['tai_khoan_doi_ung'],
                        "month_order_id" => $value['order_id'],
                        "ps_no" => 0,
                        "ps_co" => 0,
                        "thue" => 0,
                        "ma_du_an" => $value['ma_du_an'],
                        "phap_nhan" => $phap_nhan,
                        "status" => CrossCheckYear::NONE_EXIST_IN_YEAR,
                        "info_id" => $cci['id'],
                        "phap_nhan" => $value['phap_nhan'],
                        "active" => $value['active'],
                        "reason" => $value['reason'],
                    ];

                    $savedCC = $this->crossCheckYearRepository->saveData($saveData);
                    $temp['cross_year_id'] = $savedCC->id;
                    $temp['active'] = $savedCC->active;
                    $temp['reason'] = $savedCC->reason;

                    if ($value['order_id'] == null || $value['temp_order'] == 1) {
                        $temp['thang']['thu_nhap'] = 0;
                        $temp['thang']['thue'] = 0;
                        $temp['thang']['thuc_nhan'] = 0;
                    }
                    $diff[] = $temp;
                } else {
                    //Nếu tìm được khoản đối soát năm theo serial khoản tháng thì thêm dữ liệu vào mảng trả về
                    $temp['nam']['thu_nhap'] = $mergeData[$value['serial']]['ps_no'] + $mergeData[$value['serial']]['thue'];
                    $temp['nam']['thue'] = $mergeData[$value['serial']]['thue'];
                    $temp['nam']['thuc_nhan'] = $mergeData[$value['serial']]['ps_no'];

                    $temp['dien_giai'] = $mergeData[$value['serial']]['dien_giai'];
                    $temp['cross_year_id'] = $mergeData[$value['serial']]['id'];
                    $temp['additional_order'] = $mergeData[$value['serial']]['order'];
                    $temp['additional_tax'] = $mergeData[$value['serial']]['tax'];
                    $temp['active'] = $mergeData[$value['serial']]['active'];
                    $temp['reason'] = $mergeData[$value['serial']]['reason'];

                    //Lưu dữ liệu cần thiết từ tháng sang năm
                    $saveForYear = [];
                    if ($value['active'] == 0 && $mergeData[$value['serial']]['active'] != 0) {
                        $saveData = [
                            "id" => $mergeData[$value['serial']]['id'],
                            "active" => $value['active'],
                            "reason" => $value['reason'],
                        ];
                        $saveForYear = array_merge($saveForYear, $saveData);
                    }
                    if ($value['order_id'] !== null && $mergeData[$value['serial']]['month_order_id'] != $value['order_id']) {
                        $saveData = [
                            "id" => $mergeData[$value['serial']]['id'],
                            "month_order_id" => $value['order_id'],
                        ];
                        $saveForYear = array_merge($saveForYear, $saveData);
                    }
                    if (!empty($saveForYear)) {
                        $this->crossCheckYearRepository->saveData($saveData);
                    }

                    if ($value['order_id'] == null || $value['temp_order'] == 1) {
                        $temp['thang']['thu_nhap'] = 0;
                        $temp['thang']['thue'] = 0;
                        $temp['thang']['thuc_nhan'] = 0;
                    }
                    $diff[] = $temp;
                }
                //Lây ra những khoản đã được thêm vào mảng trả về để tiến hành lọc những khoản trong đs năm có mà
                //đs tháng không có
                $added[] = $temp['serial'];
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

        //Thêm những khoản đs năm mà trong đs tháng không tồn tại vào mảng trả về
        foreach ($mergeData as $key => $value) {
            if (!in_array($key, $added)) {
                $diff[] = [
                    "serial" => $key,
                    "dien_giai" => $value['dien_giai'],
                    "cross_year_id" => $value['id'],
                    "ma_chung_tu_0" => $value['ma_chung_tu_0'] == null ?? "",
                    "ma_chung_tu" => $value['ma_chung_tu'],
                    "ngay_chung_tu" => $value['ngay_chung_tu'],
                    "ma_khach" => $value['ma_khach'],
                    "order_id" => null,
                    "additional_order" => $value['order'],
                    "additional_tax" => $value['tax'],
                    "order" => null,
                    "tax" => null,
                    "active" => $value['active'],
                    "reason" => $value['reason'],
                    "thang" => [
                        "thu_nhap" => 0,
                        "thue" => 0,
                        "thuc_nhan" => 0,
                    ],
                    "nam" => [
                        "thu_nhap" => $value['ps_no'] + $value['thue'],
                        "thue" => $value['thue'],
                        "thuc_nhan" => $value['ps_no'],
                    ]
                ];
            }
        }

        $isDoneTCB = true;
        //Tính chênh lệch giữa khoản năm và tháng
        foreach ($diff as $key => &$value) {
            $value['chenh_lech'] = [
                "thu_nhap" => $value['nam']['thu_nhap'] - $value['thang']['thu_nhap'],
                "thue" => $value['nam']['thue'] - $value['thang']['thue'],
                "thuc_nhan" => $value['nam']['thuc_nhan'] - $value['thang']['thuc_nhan'],
            ];

            if ($value['active'] == 1) {
                if ($value['chenh_lech']['thuc_nhan'] != 0 || $value['chenh_lech']['thue'] != 0) {
                    if (!isset($value['additional_order'])) {
                        $isDoneTCB = false;
                    }
                }
            }
        }
        $response = [];
        $response['data'] = $diff;
        $response['done_TCB'] = $isDoneTCB;
        $response['doneAccounter'] = $cci['ke_toan_check'];

        return response()->json($response);
    }

    function showByYear(Request $request, $phap_nhan, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $cci = $this->crossCheckInfoRepository->getDataBy([
            "thang" => null,
            "phap_nhan" => $phap_nhan,
            "nam" => $nam
        ], false)->toArray();

        if (empty($cci)) {
            return Redirect::back()->with("message", [
                'title' => 'Lỗi',
                'content' => 'Không tìm thấy bộ đối soát',
                'type' => 'danger'
            ]);
        }
        return view("cross_check.show-year", compact("phap_nhan", "nam", "cci"));
    }

    function exportYear(Request $request, $phap_nhan, $nam)
    {
        Topica::canCrossOrAbort("index.cross_check_result", $phap_nhan);
        $data = $this->getByYear($request, $phap_nhan, $nam);
        $data = $data->getData()->data;

        //Export excel
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleTcb = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleAccounter = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleCompare = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font'  => array(
                'bold' => true
            ),'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

        $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "ĐỐI SOÁT CHO PHÁP NHÂN ".$phap_nhan.' NĂM '.$nam)->getStyle( 'A1' )->applyFromArray($style)->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Pháp nhân")->getStyle( 'A2' )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', strtoupper($phap_nhan));
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Năm")->getStyle( 'A3' )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', $nam);

        $objPHPExcel->getActiveSheet()->mergeCells('F4:H4');
        $objPHPExcel->getActiveSheet()->mergeCells('I4:K4');
        $objPHPExcel->getActiveSheet()->mergeCells('L4:N4');

        //header
        $objPHPExcel->getActiveSheet()->getStyle( 'A4:P5' )->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->getStyle( 'F4:H4' )->applyFromArray($styleAccounter);
        $objPHPExcel->getActiveSheet()->getStyle( 'I4:K4' )->applyFromArray($styleTcb);
        $objPHPExcel->getActiveSheet()->getStyle( 'L4:N4' )->applyFromArray($styleCompare);

        $objPHPExcel->getActiveSheet()->SetCellValue('A5', "STT");
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', "Ngày chứng từ");
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', "Mã CT");
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', "Mã khách");
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', "Diễn giải");

        $objPHPExcel->getActiveSheet()->SetCellValue('F4', "Năm")->getStyle( 'F4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('G5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('I4', "Tháng")->getStyle( 'I4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('J5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('K5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('L4', "Chênh lệch")->getStyle( 'K4' )->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue('L5', "Thu nhập");
        $objPHPExcel->getActiveSheet()->SetCellValue('M5', "Thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('N5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('O5', "Bộ thanh gốc");
        $objPHPExcel->getActiveSheet()->SetCellValue('P5', "Bộ thanh bổ sung");

        $startRender = 6;
        $lastRow = 6;

        foreach($data as $key => $row) {
            $renderRow = $key + $startRender;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$renderRow, $key + 1);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$renderRow, date("d/m/Y", strtotime($row->ngay_chung_tu)));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$renderRow, $row->ma_chung_tu);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$renderRow, $row->ma_khach);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$renderRow, implode("\n", json_decode($row->dien_giai)) );
            $objPHPExcel->getActiveSheet()->getStyle('E'.$renderRow)->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$renderRow, $row->nam->thu_nhap);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$renderRow, $row->nam->thue);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$renderRow, $row->nam->thuc_nhan);

            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$renderRow, $row->thang->thu_nhap);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$renderRow, $row->thang->thue);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$renderRow, $row->thang->thuc_nhan);

            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$renderRow, $row->chenh_lech->thu_nhap);
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$renderRow, $row->chenh_lech->thue);
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$renderRow, $row->chenh_lech->thuc_nhan);

            if( is_null($row->order_id) ) {
                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$renderRow, 'Không thấy');
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$renderRow, "F-".$row->order_id."(".$row->order->serial.")");
            }

            if($row->active == 0) {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, '');
            } else if(is_object($row->additional_order) ) {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, "F-" .$row->additional_order->id . "(" . $row->additional_order->serial . ")");
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$renderRow, '');
            }
            
            $lastRow = $renderRow;
        }

        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$startRender.':'.'P'.$lastRow )->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle( 'F'.$startRender.':'.'N'.$lastRow )->getNumberFormat()->setFormatCode('###,##0');
        $objPHPExcel->getActiveSheet()->getStyle( 'F'.($lastRow + 1).':'.'N'.($lastRow + 1) )->getNumberFormat()->setFormatCode('###,##0');

        $objPHPExcel->getActiveSheet()->SetCellValue('E'.($lastRow + 1), "Tổng")->getStyle( 'E'.($lastRow + 1) )->getFont()->setBold( true );

        $objPHPExcel->getActiveSheet()->SetCellValue('F'.($lastRow + 1), "=SUM(F{$startRender}:F{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.($lastRow + 1), "=SUM(G{$startRender}:G{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.($lastRow + 1), "=SUM(H{$startRender}:H{$lastRow})");

        $objPHPExcel->getActiveSheet()->SetCellValue('I'.($lastRow + 1), "=SUM(I{$startRender}:I{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.($lastRow + 1), "=SUM(J{$startRender}:J{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.($lastRow + 1), "=SUM(K{$startRender}:K{$lastRow})");

        $objPHPExcel->getActiveSheet()->SetCellValue('L'.($lastRow + 1), "=SUM(L{$startRender}:L{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.($lastRow + 1), "=SUM(M{$startRender}:M{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.($lastRow + 1), "=SUM(N{$startRender}:N{$lastRow})");

        foreach(range('A','P') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->SetCellValue('C'.($lastRow + 5), "Kế toán tổng hợp")->getStyle( 'C'.($lastRow + 5) )->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.($lastRow + 5), "TCB")->getStyle( 'N'.($lastRow + 5) )->getFont()->setBold( true );
        
        $textLuong = "ngoài lương";
        $fileName = "Đối soát {$textLuong}-{$nam}-{$phap_nhan}.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
}
