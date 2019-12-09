<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Helpers\ImportExcel;
use App\Http\Repositories\Contracts\CrossCheckInfoRepositoryInterface;
use App\Http\Repositories\Contracts\CrossCheckYearRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeOrderRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Http\Repositories\Contracts\CrossCheckRepositoryInterface;
use App\Http\Requests\CreateImportSalaryRequest;
use App\Http\Requests\CreateOrder;
use App\Http\Requests\EditOrderRequest;
use App\Models\OrderInfo;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Mockery\Exception;

class OrderController extends Controller
{
    protected $orderRepository;

    protected $employeeOrderRepository;

    protected $employeeRepository;

    protected $empRentRepository;

    protected $importExcel;

    protected $summaryRepository;

    protected $typeRepository;

    protected $crossCheckInfoRepository;

    protected $crossCheckYearRepository;

    protected $crossCheckRepository;

    public function __construct(OrderRepositoryInterface $orderRepository, EmployeeOrderRepositoryInterface $employeeOrderRepository,
                                EmployeeRepositoryInterface $employeeRepository, ImportExcel $importExcel, EmpRentRepositoryInterface $empRentRepository, SummaryRepositoryInterface $summaryRepository,
                                TypeRepositoryInterface $typeRepository, CrossCheckInfoRepositoryInterface $crossCheckInfoRepository,
                                CrossCheckYearRepositoryInterface $crossCheckYearRepository, CrossCheckRepositoryInterface $crossCheckRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->employeeOrderRepository = $employeeOrderRepository;
        $this->employeeRepository = $employeeRepository;
        $this->empRentRepository = $empRentRepository;
        $this->importExcel = $importExcel;
        $this->summaryRepository = $summaryRepository;
        $this->typeRepository = $typeRepository;
        $this->crossCheckInfoRepository = $crossCheckInfoRepository;
        $this->crossCheckYearRepository = $crossCheckYearRepository;
        $this->crossCheckRepository = $crossCheckRepository;

    }


    /**
     * Màn hình tạo bộ thanh toán lương
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createOrderInfoSalary(Request $request)
    {
        Topica::canOrRedirect('add.order');
        //convert data
        $temp_data = $request->all();
        $order = [];
        if (isset($temp_data['order']))
            $order = json_decode($temp_data['order'], true);
        unset($temp_data['order']);
        $data = [
            'import' => $temp_data,
            'order' => $order
        ];
        $isImportSalary = true;
//        dd($data);
        return view('orders.create', compact('isImportSalary', 'data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function importSalaryFile(Request $request)
    {

        $mappingData = config('global.mappingDataFTT');
        //convert data
        $dataExcel = [];
        $dataError = [];
        $dataWarning = [];
        $dataTable = [];
        $dataTotal = [
            'tong_thu_nhap_truoc_thue' => 0,
            'tong_non_tax' => 0,
            'tong_tnct' => 0,
            'bhxh' => 0,
            'thue_tam_trich' => 0,
            'thuc_nhan' => 0,
            'giam_tru_ban_than' => 0,
            'giam_tru_gia_canh' => 0
        ];
        if ($request->hasFile('importFile')) {
            // trường hợp import file
            $temp_data = $request->all();
            $order = [];
            if (isset($temp_data['order']))
                $order = json_decode($temp_data['order'], true);
            unset($temp_data['order']);
            $returnData = [
                'import' => $temp_data,
                'order' => $order
            ];
        } else {
            //Trường hợp đi từ màn hình bộ chứng từ
            $temp_data = $request->all();
//            dd($temp_data);
            $import = [];
            if (isset($temp_data['import']))
                $import = json_decode($temp_data['import'], true);
            unset($temp_data['import']);
            $returnData = [
                'import' => $import,
                'order' => $temp_data
            ];
        }

//        // Trường hợp có dữ liệu bảng
//        if (isset($returnData['import']['dataTable'])) {
//            $dataExcel = json_decode($returnData['import']['dataTable'], true);
//        }
//        dd($dataExcel);
        // Trường hợp import file excel
        if ($request->hasFile('importFile')) {
            $dataExcel = $this->importExcel->getDataSalary($request->file('importFile'));
            if (isset($dataExcel['dataTable'])) {
                $dataTable = $dataExcel['dataTable'];
            }
            if (isset($dataExcel['listData'])) {
                $dataExcel = $dataExcel['listData'];
            }
        }
        // validate data excel
        $dataDuplicate = [];
//        dd($dataExcel);
        if (isset($dataExcel) && $dataExcel) {
            $listDataTable = array_pluck($dataTable, 'title', 'field');
            $listDataTable['tong_tnct'] = "Tổng TNCT";
            foreach ($dataExcel as $key => &$data) {
                $data['line'] = $key + IMPORT_START_ROW;
                $data['tong_tnct'] = $data['tong_tn_truoc_thue'] - $data['tong_non_tax'];
                //tính tổng tiền
                $dataTotal['tong_thu_nhap_truoc_thue'] += $data['tong_tn_truoc_thue'];
                $dataTotal['tong_non_tax'] += $data['tong_non_tax'];
                $dataTotal['tong_tnct'] += $data['tong_tnct'];
                $dataTotal['bhxh'] += $data['bhxh'];
                $dataTotal['thue_tam_trich'] += $data['thue_tam_trich'];
                $dataTotal['thuc_nhan'] += $data['thuc_nhan'];
                $dataTotal['giam_tru_ban_than'] += $data['giam_tru_ban_than'];
                $dataTotal['giam_tru_gia_canh'] += $data['giam_tru_gia_canh'];

                // validate is not null
                checkIsNotNull(['ma_nv', 'tong_tn_truoc_thue', 'thuc_nhan', 'thue_tam_trich', 'ten_nv'], $data, $dataError, $key + IMPORT_START_ROW, $listDataTable);
                checkIsNotNumber(['tong_tn_truoc_thue', 'khac', 'com', 'thuong', 'tong_non_tax', 'bhxh', 'thuc_nhan', 'thue_tam_trich', 'giam_tru_ban_than', 'giam_tru_gia_canh'], $data, $dataError, $key + IMPORT_START_ROW, $listDataTable);
                if ($data['tong_tn_truoc_thue'] <= 0) {
                    $data['cssClass'] = 'error';
                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['tong_tn_truoc_thue']) . '</b> không có dữ liệu';
                }
                if (isset($dataDuplicate['employee_code'])) {
                    if (in_array($data['ma_nv'], $dataDuplicate['employee_code'])) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '</b> bị trùng lặp';
                    }
                }
                $dataDuplicate['employee_code'][$data['ma_nv']] = $data['ma_nv'];
            }
            // get employee in HR20
            if (!$dataError) {
                $employees = $this->employeeRepository->getDataBy(['employee_code' => $dataDuplicate['employee_code']], false);
                foreach ($dataExcel as $key => &$data) {
                    $e = $employees->firstWhere('employee_code', $data['ma_nv']);
                    if ($e) {
                        $dataDuplicate['employee_code'][$e->cmt] = $e->cmt;
                        //validate tên
                        if (str_slug($e->last_name . ' ' . $e->first_name, '_') != str_slug($data['ten_nv'], '_')) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ten_nv']) . '</b> không chính xác (tên đúng là: ' . $e->last_name . ' ' . $e->first_name . ')';
                        }
                    } else {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '</b> không tồn tại trên HR20';
                    }
                }
            }
            // validate com,thuong,ftt
            if (!$dataError) {
                $ftt = $this->summaryRepository->getDataFttOfEmployee([
                    'employee_code' => $dataDuplicate['employee_code'],
                    'phap_nhan' => $returnData['order']['phap_nhan'],
                    'month' => $returnData['import']['month'],
                    'year' => $returnData['import']['year']
                ], false);

                $type = $this->typeRepository->getDataBy([], false)->pluck('name', 'id');
                // list com
                $list_com = [];
                $type->search(function ($item, $key) use (&$list_com) {
                    if (stristr($item, 'Com NV')) {
                        $list_com[] = $key;
                    }
                });
                $list_thuong = [];
                $type->search(function ($item, $key) use (&$list_thuong) {
                    if (stristr($item, 'Thưởng NV')) {
                        $list_thuong[] = $key;
                    }
                });
                if ($ftt) {
                    foreach ($dataExcel as $key => &$data) {
                        //check is employee has Salary
                        $hasSalary = $ftt->where('type', 1)->where('employee_code', $data['ma_nv'])->first();
                        if ($hasSalary) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '(' . $data['ma_nv'] . ')' . '</b> đã tồn tại thông tin lương tại F-' . $hasSalary['id'];
                        }
//                        // check com
//                        if (count($list_com) > 0) {
//                            $com = 0;
//                            foreach ($list_com as $l1) {
//                                $com += $ftt->where('employee_code', $data['ma_nv'])->where('type', $l1)->sum('tong_tnct');
//                            }
//                            if ($com != $data['com']) {
//                                $data['cssClass'] = 'error';
//                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '(' . $data['ma_nv'] . ')' . '</b> có <b>COM</b> không chính xác(' . number_format($com) . ')';
//                            }
//                        }
//
//                        // check thưởng
//                        if (count($list_thuong) > 0) {
//                            $thuong = 0;
//                            foreach ($list_thuong as $l1) {
//                                $thuong += $ftt->where('employee_code', $data['ma_nv'])->where('type', $l1)->sum('tong_tnct');
//                            }
//                            if ($thuong != $data['thuong']) {
//                                $data['cssClass'] = 'error';
//                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '(' . $data['ma_nv'] . ')' . '</b> có <b>Thưởng</b> không chính xác(' . number_format($thuong) . ')';
//                            }
//                        }
//
//                        //check khac
//                        $l = [];
//                        $type->search(function ($item, $key) use (&$l) {
//                            if (stristr($item, 'Thưởng NV')) {
//                                $l[] = $key;
//                            }
//                            if (stristr($item, 'Com NV')) {
//                                $l[] = $key;
//                            }
//                        });
//                        if (count($l) > 0) {
//                            $khac = $ftt->where('employee_code', $data['ma_nv']);
//                            foreach ($l as $l1) {
//                                $khac = $khac->where('type', '!=', $l1);
//                            }
//                            $khac = $khac->sum('tong_tnct');
//                            if ($khac != $data['khac']) {
//                                $data['cssClass'] = 'error';
//                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '(' . $data['ma_nv'] . ')' . '</b> có <b>Khác</b> không chính xác(' . number_format($khac) . ')';
//                            }
//                        }
                        // check money
//                        $total = $com + $thuong + $khac;
//                        if ($total > $data['tong_tnct']) {
//                            $data['cssClass'] = 'error';
//                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($listDataTable['ma_nv']) . '(' . $data['ma_nv'] . ')' . '</b> có có tổng thu nhập chịu thuế (' . $data['tong_tnct'] . ') nhỏ hơn tiền các chứng từ (' . $total . ')';
//                        }
//                        validateMoneyOfSalaryExcelVer2(['tong_tn_truoc_thue', 'tong_non_tax', 'tong_tnct', 'bhxh', 'thue_tam_trich', 'thuc_nhan'], $data, $dataError, $key, $listDataTable, $ftt);

                    }
                }
            }
        }

        // check tổng thực nhận

        if ($dataExcel && number_format($dataTotal['thuc_nhan']) != number_format($returnData['order']['so_tien'])) {
            $dataError[] = "Tổng số tiền thực nhận từ các chứng từ (<b>" . number_format($dataTotal['thuc_nhan']) . "</b>) không khớp với tổng sô tiền thực nhận trong bộ thanh toán (<b>" . number_format($returnData['order']['so_tien']) . "</b>)";
        }


        $dataTable[] = [
            'title' => 'Tổng TNCT',
            'field' => 'tong_tnct',
            'visible' => true,
            "formatter" => "money",
            "formatterParams" => ["precision" => 0]
        ];

        if (isset($returnData['order']['view']))
            $returnData['view'] = $returnData['order']['view'];
        if (isset($returnData['import']['month']))
            $returnData['month'] = $returnData['import']['month'];
        if (isset($returnData['import']['year']))
            $returnData['year'] = $returnData['import']['year'];
        if (isset($returnData['order']['phap_nhan']))
            $returnData['phap_nhan'] = $returnData['order']['phap_nhan'];

        sort($dataError, SORT_NATURAL);
//        dd($dataTable);
        return view('vouchers.import', [
            'request' => $returnData,
            'dataTable' => $dataTable,
            'dataExcel' => $dataExcel,
            'dataError' => $dataError,
            'total' => $dataTotal,
            'dataWarning' => $dataWarning
        ]);
    }

    /**
     * save file bang lương
     * @param CreateImportSalaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveSalaryFile(Request $request)
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 0);
        Topica::canOrRedirect('add.order');
        $data = $request->all();
        DB::beginTransaction();
        try {
            $order = json_decode($data['order'], true);
//            dd($order);
            if (isset($data['phap_nhan']))
                $order['phap_nhan'] = $data['phap_nhan'];
            if (isset($data['month']))
                $order['month'] = $data['month'];
            if (isset($data['year']))
                $order['year'] = $data['year'];
            $dataTable = json_decode($data['dataExcel'], true);
            $isSalary = isset($order['view']) && $order['view'] == 'import-salary';
            // trường hợp import bảng lương
            $order['isSalary'] = 1;
            $orderExist = $this->summaryRepository->getDataBy(['type' => SALARY, 'status' => 0, 'phap_nhan' => $order['phap_nhan'], 'month' => $order['month'], 'year' => $order['year']], false)->toArray();
//            dd($orderExist);
            if ($orderExist) {
                $check = $this->summaryRepository->delete(['type' => SALARY, 'status' => 0, 'phap_nhan' => $order['phap_nhan'], 'month' => $order['month'], 'year' => $order['year']]);
            }

            $dataDuplicate = [];
            foreach ($dataTable as &$t) {
                $t['tong_thu_nhap_truoc_thue'] = $t['tong_tn_truoc_thue'];
                $t['employee_code'] = $t['ma_nv'];
                $t['data'] = json_encode($t['value']);
                $t['note'] = "Lương NV";
                $t['employee_table'] = "employees";
                $t['month'] = $order['month'];
                $t['year'] = $order['year'];

                if (!isset($t['sum_thu_nhap_truoc_thue']))
                    $t['sum_thu_nhap_truoc_thue'] = $t['tong_thu_nhap_truoc_thue'];
                if (!isset($t['sum_non_tax']))
                    $t['sum_non_tax'] = $t['tong_non_tax'];
                if (!isset($t['sum_tnct']))
                    $t['sum_tnct'] = $t['tong_tnct'];
                if (!isset($t['sum_bhxh']))
                    $t['sum_bhxh'] = $t['bhxh'];
                if (!isset($t['sum_thue_tam_trich']))
                    $t['sum_thue_tam_trich'] = $t['thue_tam_trich'];
                if (!isset($t['sum_thuc_nhan']))
                    $t['sum_thuc_nhan'] = $t['thuc_nhan'];
                if (!isset($t['da_thanh_toan']))
                    $t['da_thanh_toan'] = $t['da_thanh_toan'];
                if (!isset($t['con_lai_can_thanh_toan']))
                    $t['con_lai_can_thanh_toan'] = $t['con_lai_can_thanh_toan'];
                if (!isset($t['thue_da_trich']))
                    $t['thue_da_trich'] = $t['thue_da_trich'];
                $dataDuplicate['employee_code'][$t['ma_nv']] = $t['ma_nv'];


            }
            // get cmt
            $employees = $this->employeeRepository->getDataBy(['employee_code' => $dataDuplicate['employee_code']], false);
            foreach ($dataTable as &$t) {
                $e = $employees->firstWhere('employee_code', $t['ma_nv']);
                if ($e) {
                    $dataDuplicate['employee_code'][$e->cmt] = $e->cmt;
                }
            }


            $ftt = $this->summaryRepository->getDataFttOfEmployee([
                'employee_code' => $dataDuplicate['employee_code'],
                'phap_nhan' => $order['phap_nhan'],
                'month' => $order['month'],
                'year' => $order['year']
            ], false);
            foreach ($dataTable as &$t) {
                $t['tong_thu_nhap_truoc_thue'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_thu_nhap_truoc_thue');
                $t['tong_non_tax'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_non_tax');
                $t['tong_tnct'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_tnct');
                $t['bhxh'] -= $ftt->where('employee_code', $t['employee_code'])->sum('bhxh');
                $t['thue_tam_trich'] -= $ftt->where('employee_code', $t['employee_code'])->sum('thue_tam_trich');
                $t['thuc_nhan'] -= $ftt->where('employee_code', $t['employee_code'])->sum('thuc_nhan');
            }

            $saveOrder = $this->orderRepository->saveData($order, $dataTable);
            if (!$saveOrder)
                throw new \Exception('Error');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
        return redirect()->route('order.listOrders');
    }

    /**
     * Validate dữ liệu phap nhân với tháng
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validatePhapNhanMonthYear(Request $request)
    {
        $data = $request->all();

        try {

            // Kiểm tra đã có pháp nhân
            if (!(isset($data['phap_nhan']) && $data['phap_nhan'])) {
                throw new Exception('Không có pháp nhân');
            }

            // Kiểm tra đã có tháng
            if (!(isset($data['month']) && $data['month'])) {
                throw new Exception('Không có tháng');
            }

            // Kiểm tra năm
            if (!(isset($data['year']) && $data['year'])) {
                throw new Exception('Không có năm');
            }
            $data['status'] = 0;
            $data['type'] = SALARY;
            // Kiểm dữ liệu từ pháp nhân, tháng, năm
            $order = $this->summaryRepository->getDataBy($data, false)->toArray();
            if ($order) {
                throw new Exception('Bảng lương của pháp nhân ' . $data['phap_nhan'] . ' tại tháng ' . $data['month'] . '.' . $data['year'] . ' đã nhập rồi. Bạn có muốn thay thế hay không?');
            }
            return response()->json([
                'status' => 1,
                'data' => $data,
                'message' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'data' => $data,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function createUI(Request $request)
    {
        Topica::canOrRedirect('add.order');
        $data = $request->all();
        $request->session()->forget('backToCrossCheck');
        if (isset($data['temp_order']) && $data['temp_order'] && isset($data['cross_check_info_id']) && $data['cross_check_info_id']) {
            $crossInfoId = $data['cross_check_info_id'];

            $request->session()->put('backToCrossCheck', [
                'info_id' => $crossInfoId
            ]);
        }
        return view('orders.create', compact("data"));
    }

    public function saveOrder(Request $request)
    {
        $data = $request->all();
        $type = $this->typeRepository->getDataBy([], false)->pluck('name', 'id');
        DB::beginTransaction();
        try {
            $order = $data['order'];
//            d($order['phap_nhan']."12");
            if (!isset($data['vouchers'])) {
                $result = [];
                $result['result'] = 'fail';
                $result['message'] = "Bạn cần nhập thông tin chứng từ cho bộ thanh toán";
                echo json_encode($result);
                exit();
            }

            $sumTax = 0;
            if (isset($order['additional_order']) && $order['additional_order'] == 0) {
                $order['additional_order'] = urlencode(null);
            }
            if (isset($order['ma_du_toan'])) {
                foreach ($order as $key => $value) {
                    $order[$key] = urldecode($value) == "" ? null : urldecode($value);
                }
                if (!isset($order['id'])) {
                    Topica::canOrAbort('add.order');
                    $order['ngay_de_xuat'] = str_replace('/', '-', $order['ngay_de_xuat']);
                    $order['month'] = date('m', strtotime($order['ngay_de_xuat']));
                    $order['year'] = date('Y', strtotime($order['ngay_de_xuat']));
                    $order['san_pham'] = $order['san_pham'];
                    //Nếu là bộ bổ sung
                    if (isset($order['additional_order'])) {
                        $order['reference_id'] = intval($order['additional_order']) == -1 ? null : intval($order['additional_order']);
                        $order['additional_order'] = 1;
                    }
                    $saveOrder = $this->orderRepository->saveData($order);

                    $orderId = $saveOrder->id;
                    $phapNhan = $saveOrder->phap_nhan;
                    $sanPham = $saveOrder->san_pham;

                    if (isset($order['additional_order'])) {
                        $this->crossCheckYearRepository->saveData([
                            'id' => intval($order['cross_id']),
                            'order_id' => $orderId
                        ]);
                    }
                } else {
                    $orderId = $order['id'];
                    $phapNhan = $order['phap_nhan'];
                    $sanPham = $order['san_pham'];
                    $checkOrder = $this->orderRepository->getDataBy(['id' => $orderId], false)->first();
                    Topica::canOrAbort('edit.order', $checkOrder);
                }

                $vouchers = $data['vouchers'];

                $hasErrors = false;
                $dataEmpOrder = [];
                $sum = 0;

                foreach ($vouchers as $key => $value) {
                    if ($value['status'] == 'stable') {
                        $sum += floatval($vouchers[$key]['payment_value']) - floatval($vouchers[$key]['personal_tax']);
                        continue;
                    }
                    if ($value['status'] == 'deleted') {
                        $delete = $this->summaryRepository->delete([
                            "id" => $value['id']
                        ]);
                        unset($vouchers[$key]);
                        continue;
                    }
                    $empOrder = [];
                    $vouchers[$key]['data_validate'] = true;
                    $vouchers[$key]['message'] = '';
//                    if ($vouchers[$key]['payment_value'] == 0) {
//                        $vouchers[$key]['data_validate'] = false;
//                        $vouchers[$key]['message'] .= 'payment_value|';
//                        $hasErrors = true;
//                        continue;
//                    }

                    if (!in_array($vouchers[$key]['payment_type'], $type->toArray())) {
                        $vouchers[$key]['data_validate'] = false;
                        $vouchers[$key]['message'] .= 'payment_type|';
                        $hasErrors = true;
                        continue;
                    }

                    if (!in_array($vouchers[$key]['identity_type'], ['cmt', 'hc', 'mnv'])) {
                        $vouchers[$key]['data_validate'] = false;
                        $vouchers[$key]['message'] .= 'identity_type|';
                        $hasErrors = true;
                        continue;
                    }

                    if ($vouchers[$key]['input_status'] == "search-fail") {
                        if (empty(trim($vouchers[$key]['emp_name']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'emp_name|';
                            $hasErrors = true;
                        }
                        // if (empty(trim($vouchers[$key]['emp_code_place']))) {
                        //     $vouchers[$key]['data_validate'] = false;
                        //     $vouchers[$key]['message'] .= 'emp_code_place|';
                        //     $hasErrors = true;
                        // }
                        // if (empty(trim($vouchers[$key]['emp_code_date']))) {
                        //     $vouchers[$key]['data_validate'] = false;
                        //     $vouchers[$key]['message'] .= 'emp_code_date|';
                        //     $hasErrors = true;
                        // }
                        if (empty(trim($vouchers[$key]['emp_country']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'emp_country|';
                            $hasErrors = true;
                        }
                        if (empty(trim($vouchers[$key]['emp_live_status']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'emp_live_status|';
                            $hasErrors = true;
                        }
                        if (empty(trim($vouchers[$key]['identity_code']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'identity_code|';
                            $hasErrors = true;
                        }
                        if (empty(trim($vouchers[$key]['identity_code']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'identity_code|';
                            $hasErrors = true;
                        }
                        if (empty(trim($vouchers[$key]['emp_pos']))) {
                            $vouchers[$key]['data_validate'] = false;
                            $vouchers[$key]['message'] .= 'emp_pos|';
                            $hasErrors = true;
                        }
                    }

                    if ($hasErrors) {
                        continue;
                    } else if ($vouchers[$key]['input_status'] == "search-fail") {
                        $empRentAttr = ['identity_code', 'identity_type', 'emp_code_date', 'emp_code_place', 'emp_name', 'emp_tax_code', 'emp_country', 'emp_live_status', 'emp_account_number', 'emp_account_bank'];
                        $empRentInfo = [];
                        foreach ($empRentAttr as $item) {
                            $empRentInfo[$item] = empty($vouchers[$key][$item]) ? "" : $vouchers[$key][$item];
                        }
                        $empRentInfo['emp_live_status'] = ($empRentInfo['emp_live_status'] == 'Có') ? 1 : 0;
                        $empRent = $this->empRentRepository->saveData($empRentInfo);
                    }

                    $filter = ['COM' => 'com', 'Thưởng' => 'bonus', 'Thuê khoán' => 'rent', 'Lãi vay' => 'interest'];
                    if (isset($vouchers[$key]['id']) && $vouchers[$key]['status'] == 'updated') {
                        $empOrder['id'] = $vouchers[$key]['id'];
                    }
                    $empOrder['employee_code'] = $vouchers[$key]['identity_code'];
                    $empOrder['phap_nhan'] = $phapNhan;
                    $empOrder['san_pham'] = $sanPham;
                    $empOrder['vi_tri'] = $vouchers[$key]['emp_pos'];
                    $empOrder['order_id'] = $orderId;
                    $empOrder['employee_table'] = $vouchers[$key]['identity_type'] == "mnv" ? "employees" : "employee_rent";
                    $empOrder['type'] = array_keys($type->toArray(), $vouchers[$key]['payment_type'])[0];
                    $empOrder['tong_thu_nhap_truoc_thue'] = $vouchers[$key]['payment_value'];
                    $empOrder['tong_tnct'] = $vouchers[$key]['payment_value'];
                    $empOrder['thue_tam_trich'] = $vouchers[$key]['personal_tax'];
                    $empOrder['thuc_nhan'] = floatval($vouchers[$key]['payment_value']) - floatval($vouchers[$key]['personal_tax']);
                    $dataEmpOrder[] = $empOrder;

                    $sum += $empOrder['thuc_nhan'];
                    $sumTax += $empOrder['thue_tam_trich'];
                }

                if (isset($order['additional_order'])) {
                    $reference_order = [];
                    $reference_order['quy_doi'] = 0;
                    $reference_order['thue'] = 0;
                    if (intval($order['additional_order']) !== -1) {
                        $reference_order = $this->orderRepository->getDataBy([
                            'order_info.id' => $order['reference_id']
                        ], false, true)->first()->toArray();
                    }

                    $cross = $this->crossCheckYearRepository->getDataBy([
                        "id" => $order['cross_id']
                    ])->first()->toArray();

                    if ($reference_order['quy_doi'] + $sum != $cross['ps_no'] && $reference_order['thue'] + $sumTax != $cross['thue']) {
                        throw new \Exception("Tổng số tiền bộ bổ sung không khớp với bộ đối soát năm.");
                    }
                }

                if ($hasErrors) {
                    $result = [];
                    $result['result'] = 'fail';
                    $result['data'] = $vouchers;
                    DB::rollback();
                    echo json_encode($result);
                    exit();
                } else {
                    if ($sum != $order['quy_doi']) {
                        throw new \Exception("Tổng số tiền chứng từ không bằng số tiền trên bộ thanh toán {$sum} != {$order['quy_doi']}");
                    }
                    $employeeOrder = $this->summaryRepository->saveManyData($dataEmpOrder);
                    if (isset($order['temp_order']) && $order['temp_order'] == 'true') {
                        $result = [];
                        $result['result'] = 'redirect';
                        $result['url'] = route("cross_check.pickOrders", [
                            'order_id' => $orderId,
                            'cross_check_info_id' => $order['cross_check_info_id']
                        ]);
                        echo json_encode($result);
                    } else if (isset($order['additional_order'])) {
                        $crossCheckYear = $this->crossCheckInfoRepository->getDataBy([
                            "id" => $order['cross_check_info_id']
                        ], false)->first()->toArray();

                        $result = [];
                        $result['result'] = 'redirect';
                        $result['url'] = route("cross_check.showByYear", [
                            "phap_nhan" => $crossCheckYear['phap_nhan'],
                            "nam" => $crossCheckYear['nam'],
                        ]);
                        echo json_encode($result);
                    } else {
                        $result = [];
                        $result['result'] = 'success';
                        echo json_encode($result);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $result = [];
            $result['result'] = 'fail';
            $result['message'] = $e->getMessage();
            echo json_encode($result);
            exit();
        }
    }


    public function getData(Request $request)
    {
        Topica::can('index.order');
        $requestParams = $request->all();
        if (Topica::can('index.order') === 'index.order.self') {
            $requestParams['created_by'] = Auth::user()->id;
        }
        $data = $this->orderRepository->getDataBy($requestParams, true, true);
        return $data;
    }

    public function listOrders()
    {
        Topica::canOrRedirect('index.order');
        return view('orders.list');
    }

    public function importFileFTT(Request $request)
    {
//        dd($request->all());
        Topica::canOrRedirect('add.order');

        $start = microtime(true);
        $dataRequest = $request->all();
//        dd($dataRequest);
        $type = $this->typeRepository->getDataBy([], false)->pluck('name')->toArray();
        $validatePositionRule = [
            "title" =>      ["Nhân viên cùng pháp nhân", "Nhân viên khác pháp nhân", "CTV cùng pháp nhân", "CTV khác pháp nhân", "Nhân viên thuê khoán"],
            "Lương NV"=>      [1, 0, 0, 0, 0],
            "Lương CTV"=>     [0, 0, 1, 0, 0],
            "Com NV"=>        [1, 0, 0, 0, 0],
            "Com CTV"=>       [0, 0, 1, 0, 0],
            "Thưởng NV"=>     [1, 0, 0, 0, 0],
            "Thưởng CTV"=>    [0, 0, 1, 0, 0],
            "TKCM"=>          [2, 1, 1, 1, 1]
        ];
        $NV_SAME = 0;
        $NV_DIFF = 1;
        $CTV_SAME = 2;
        $CTV_DIFF = 3;
        $RENT = 4;
        $mappingData = config('global.mappingDataFTT');
        $dataExcel = [];
        $dataError = [];
        $dataWarning = [];
        $dataValidateDupilicate = [];
        $dataTotal = [
            'tong_thu_nhap_truoc_thue' => 0,
            'tong_non_tax' => 0,
            'tong_tnct' => 0,
            'bhxh' => 0,
            'thue_tam_trich' => 0,
            'thuc_nhan' => 0,
            'giam_tru_ban_than' => 0,
            'giam_tru_gia_canh' => 0
        ];

        try {
            $order = isset($dataRequest['order']) ? json_decode($dataRequest['order'], true) : null;
            $isSalary = (isset($order['view']) && $order['view'] == 'import-salary') || (isset($dataRequest['view']) && $dataRequest['view'] == 'import-salary');
//            dd($dataRequest);
            if (isset($dataRequest['order'])) {
                $r = json_decode($dataRequest['order'], true);
            } else {
                $r = $dataRequest;
            }

            if ($isSalary) {
                $import = isset($dataRequest['import']) ? json_decode($dataRequest['import'], true) : [];
                $r['month'] = isset($import['month']) ? $import['month'] : (isset($dataRequest['month']) ? $dataRequest['month'] : "");
                $r['year'] = isset($import['year']) ? $import['year'] : (isset($dataRequest['year']) ? $dataRequest['year'] : "");

                //kiểm tra month phải dược chọn
//                dd($dataRequest['month']);
                if (!(isset($r['month']) && $r['month'])) {
                    $dataError[] = "Bạn phải nhập tháng";
                    return view('vouchers.import', [
                        'request' => $r,
                        'dataExcel' => [],
                        'dataError' => $dataError,
                        'total' => $dataTotal
                    ]);
                }
            }
            if (isset($dataRequest['import'])) {
                $import = json_decode($dataRequest['import'], true);
                $r['month'] = isset($import['month']) ? $import['month'] : '';
                $r['year'] = isset($import['year']) ? $import['year'] : '';
                if (isset($import['dataExcel']))
                    $dataExcel = json_decode($import['dataExcel'], true);
//            dd($import);
            }
//        dd($post_max_size = (ini_get('upload_max_filesize')));
//            dd($request->file('importFile'));
            if ($request->file('importFile') && $request->file('importFile')->getError()) {
                $dataError[] = $request->file('importFile')->getErrorMessage();
            }
            if ($request->hasFile('importFile')) {

                $r = json_decode($dataRequest['order'], true);
                if ($isSalary) {

                    $r['month'] = $dataRequest['month'];
                    $r['year'] = $dataRequest['year'];

                    //kiểm tra month phải dược chọn
//                dd($dataRequest['month']);
                    if (!(isset($dataRequest['month']) && $dataRequest['month'])) {
                        $dataError[] = "Bạn phải nhập tháng";
                        return view('vouchers.import', [
                            'request' => $r,
                            'dataExcel' => [],
                            'dataError' => $dataError,
                            'total' => $dataTotal
                        ]);
                    }
                }


                // get data by file Excel
                $dataExcel = $this->importExcel->getDataFTT($request->file('importFile'));
            }

            // get data ftt
            // check chứng từ trong tháng
            if ($isSalary) {
                $ftt = $this->summaryRepository->getDataBy([
                    'ngay_thanh_toan' => [
                        'month' => $r['month'],
                        'year' => $r['year']
                    ],
                    'phap_nhan' => $r['phap_nhan'],
                    'notSalaryEmployee' => true
                ], false);
            }
//        dd($ftt);
            if ($dataTotal) {
//            dd($dataExcel);
                foreach ($dataExcel as $key => &$data) {
//                dd($data);
                    $data['line'] = $key + IMPORT_START_ROW_FTT;
                    //tính tổng tiền
                    $dataTotal['tong_thu_nhap_truoc_thue'] += $data['tong_thu_nhap_truoc_thue'];
                    $dataTotal['tong_non_tax'] += $data['tong_non_tax'];
                    $dataTotal['tong_tnct'] += $data['tong_tnct'];
                    $dataTotal['bhxh'] += $data['bhxh'];
                    $dataTotal['thue_tam_trich'] += $data['thue_tam_trich'];
                    $dataTotal['thuc_nhan'] += $data['thuc_nhan'];
                    $dataTotal['giam_tru_ban_than'] += $data['giam_tru_ban_than'];
                    $dataTotal['giam_tru_gia_canh'] += $data['giam_tru_gia_canh'];

                    // validate bảng lương
                    if ($isSalary) {

                        $data['month'] = $r['month'];
                        $data['year'] = $r['year'];
                        if ($data['note'] != SALARY) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : Dữ liệu import phải là <b>' . SALARY . '</b>';
                        }
                        if (!$data['employee_code']) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['employee_code']['title']) . '</b> không được để trống';
                        } else {
//                        dd($ftt);
                            // so sánh các dữ liệu lương 03 có nhỏ hơn số tiền trong bộ chứng từ
                            validateMoneyOfSalaryExcel(['tong_thu_nhap_truoc_thue', 'tong_non_tax', 'tong_tnct', 'bhxh', 'thue_tam_trich', 'thuc_nhan'], $data, $dataError, $key, $mappingData, $ftt);


                        }


                    }
                    if (!$isSalary) {
                        if ($data['note'] == SALARY) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : Dữ liệu import không được là <b>' . SALARY . '</b>';
                        }
                    }

                    // validate dữ liệu không để trống
                    if (!$data['emp_name']) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_name']['title']) . '</b> không được để trống';
                    }
                    if (!$data['tong_thu_nhap_truoc_thue']) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['tong_thu_nhap_truoc_thue']['title']) . '</b> không được để trống';
                    }
                    if ($data['tong_tnct'] . '' == null) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['tong_tnct']['title']) . '</b> không được để trống';
                    }
                    if ($data['thue_tam_trich'] . '' == null) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['thue_tam_trich']['title']) . '</b> không được để trống';
                    }

                    if ($data['thuc_nhan'] . '' == null) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['thuc_nhan']['title']) . '</b> không được để trống';
                    }
                    if (!$data['note']) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['note']['title']) . '</b> không được để trống';
                    }
                    if (!$data['emp_tax_code'] && !$data['employee_code'] && !$data['identity_code']) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_tax_code']['title']) . ',' . ($mappingData['employee_code']['title']) . ',' . ($mappingData['identity_code']['title']) . '</b> không được đồng thời để trống';
                    }

                    if ($isSalary) {
                        // validate mã nhân viên, ID, mã số thuế bị trùng lặp
                        if (isset($dataValidateDupilicate['emp_tax_code']) && in_array($data['emp_tax_code'], $dataValidateDupilicate['emp_tax_code'])) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_tax_code']['title']) . '</b> bị trùng lặp';
                        }
                        if (isset($dataValidateDupilicate['employee_code']) && in_array($data['employee_code'], $dataValidateDupilicate['employee_code'])) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['employee_code']['title']) . '</b> bị trùng lặp';
                        }
                        if (isset($dataValidateDupilicate['identity_code']) && in_array($data['identity_code'], $dataValidateDupilicate['identity_code'])) {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['identity_code']['title']) . '</b> bị trùng lặp';
                        }
                    }
                    if ($data['emp_tax_code']) {
                        $dataValidateDupilicate['emp_tax_code'][$data['emp_tax_code']] = $data['emp_tax_code'];
                    }
                    if ($data['employee_code']) {
                        $dataValidateDupilicate['employee_code'][$data['employee_code']] = $data['employee_code'];
                    }
                    if ($data['identity_code']) {
                        $dataValidateDupilicate['identity_code'][$data['identity_code']] = $data['identity_code'];
                    }

                    // validate dữ liệu kiểu số
                    checkIsNumber(['emp_tax_code', 'tong_thu_nhap_truoc_thue', 'tong_non_tax', 'tong_tnct', 'bhxh', 'thue_tam_trich', 'thuc_nhan', 'giam_tru_ban_than', 'giam_tru_gia_canh'], $data, $dataError, $key, $mappingData);
//                checkIsNotZero(['thu_nhap_truoc_thue', 'thuc_nhan'], $data, $dataError, $key, $mappingData);
                    if (!in_array($data['note'], $type)) {
                        $data['cssClass'] = 'error';
                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['note']['title']) . '</b> không chính xác';
                    }
                }

//kiểm tra số tiền trong bộ thanh toán có khớp với tổng thực nhận hay không?
                if ($dataExcel && number_format($r['so_tien']) != number_format($dataTotal['thuc_nhan'])) {
                    $dataError[] = "Tổng số tiền thực nhận từ các chứng từ (<b>" . number_format($dataTotal['thuc_nhan']) . "</b>) không khớp với tổng sô tiền thực nhận trong bộ thanh toán (<b>" . number_format($r['so_tien']) . "</b>)";
                }
                // Kiểm tra mã nhân viên có tồn tại trong HR20
                // TODO: kiểm tra ID và Mã số thuế có trong HR20? và validate mã nhân viên với ID và mã số thuế
                if (isset($dataValidateDupilicate['employee_code'])) {
                    $employee = $this->employeeRepository->getDataBy(['employee_code' => $dataValidateDupilicate['employee_code']], false);
                }
                if (isset($dataValidateDupilicate['identity_code'])) {
                    $employeeRent = $this->empRentRepository->getDataBy(['with_trash'=>true,'identity_code' => $dataValidateDupilicate['identity_code']], false);
                    $employeeByID = $this->employeeRepository->getDataBy(['identity_code' => $dataValidateDupilicate['identity_code']], false);
                }
                if (isset($dataValidateDupilicate['emp_tax_code'])) {
                    $employeeRentTax = $this->empRentRepository->getDataBy(['emp_tax_code' => $dataValidateDupilicate['emp_tax_code']], false);
                }

                foreach ($dataExcel as $key => &$data) {
                    if ($data['employee_code']) {
                        // validate mã nhân viên
                        if (isset($employee) && $employee) {
                            $emp = $employee->firstWhere('employee_code', $data['employee_code']);
                            if ($emp) {

                                if (trim(str_slug($emp->last_name . ' ' . $emp->first_name, '-')) != trim(str_slug($data['emp_name'], '-'))) {
                                    $data['cssClass'] = 'error';
                                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_name']['title']) . '</b> so với mã nhân viên không chính xác';
                                }
                                if ($emp->identity_code != $data['identity_code']) {
                                    $data['cssClass'] = 'error';
                                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['identity_code']['title']) . '</b> không chính xác';
                                }
                                $data['employee_table'] = "employees";

                                $paymentType = isset($validatePositionRule[$data['note']]) ? $data['note'] : "TKCM";

                                if (array_key_exists("phap_nhan", $r) && array_key_exists("note", $data)) {
                                    if ($r['phap_nhan'] != $emp->phap_nhan) {
                                        $empPos = $emp->vi_tri == "V" ? $CTV_DIFF : $NV_DIFF;
                                        $resVal = $validatePositionRule[$paymentType][$empPos];
                                        if ($resVal == 0) {
                                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> không thể có '. $data['note'];
                                        } else if ($resVal == 2) {
                                            $dataWarning['warning'][] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> đang có '. $data['note'];
                                        }
                                    } else {
                                        $empPos = $emp->vi_tri == "V" ? $CTV_SAME : $NV_SAME;
                                        $resVal = $validatePositionRule[$paymentType][$empPos];
                                        if ($resVal == 0) {
                                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> không thể có '. $data['note'];
                                        } else if ($resVal == 2) {
                                            $dataWarning['warning'][] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> đang có '. $data['note'];
                                        }
                                    }
                                }
                                // TODO: check mã số thuế
//                        if ($emp->identity_code != $data['emp_tax_code']) {
//                            $data['cssClass'] = 'error';
//                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_tax_code']['title']) . '</b> không chính xác';
//                        }
                            } else {
                                $data['cssClass'] = 'error';
                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : không tồn tại dữ liệu trên HR20';
                            }
                        } else {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : không tồn tại dữ liệu trên HR20';
                        }
                    } elseif ($data['identity_code']) {
                        $flagIsEmployee = false;

                        // validate ID
                        if (isset($employeeByID) && $employeeByID) {
                            $employeeID = $employeeByID->firstWhere('cmt', '===',$data['identity_code']);

                            if ($employeeID) {
                                if ($employeeID->last_name . ' ' . $employeeID->first_name != $data['emp_name']) {
                                    $data['cssClass'] = 'error';
                                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_name']['title']) . '</b> so với mã nhân viên không chính xác';
                                }
                                $data['employee_table'] = "employees";

                                $paymentType = isset($validatePositionRule[$data['note']]) ? $data['note'] : "TKCM";

                                if (array_key_exists("phap_nhan", $r) && array_key_exists("note", $data)) {
                                    if ($r['phap_nhan'] != $employeeID->phap_nhan) {

                                        $empPos = $employeeID->vi_tri == "V" ? $CTV_DIFF : $NV_DIFF;
                                        $resVal = $validatePositionRule[$paymentType][$empPos];
                                        if ($resVal == 0) {
                                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> không thể có '. $data['note'];
                                        } else if ($resVal == 2) {
                                            $dataWarning['warning'][] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> đang có '. $data['note'];
                                        }
                                    } else {
                                        $empPos = $employeeID->vi_tri == "V" ? $CTV_SAME : $NV_SAME;
                                        $resVal = $validatePositionRule[$paymentType][$empPos];
                                        if ($resVal == 0) {
                                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> không thể có '. $data['note'];
                                        } else if ($resVal == 2) {
                                            $dataWarning['warning'][] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$empPos]) . '</b> đang có '. $data['note'];
                                        }
                                    }
                                }
                                $data['employee_code'] = $employeeID->employee_code;
                                $flagIsEmployee = true;
                            } else {
                                $flagIsEmployee = false;
                            }
                        }
                        if ($flagIsEmployee) {
                            continue;
                        }
                        if (isset($employeeRent) && $employeeRent) {
                            $empRent = $employeeRent->firstWhere('identity_code', '===',$data['identity_code'].'');
//
                            if ($empRent) {
                                if (trim(str_slug($empRent->emp_name)) != trim(str_slug($data['emp_name']))) {
                                    $data['cssClass'] = 'error';
                                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_name']['title']) . '</b> so với ID không chính xác';
                                }
                                // TODO: check mã số thuế
//                            if ($emp->identity_code != $data['emp_tax_code']) {
//                                $data['cssClass'] = 'error';
//                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_tax_code']['title']) . '</b> không chính xác';
//                            }

                                $data['employee_table'] = "employee_rent";

                                $paymentType = isset($validatePositionRule[$data['note']]) ? $data['note'] : "TKCM";
                                if (array_key_exists("phap_nhan", $r) && array_key_exists("note", $data)) {
                                    $resVal = $validatePositionRule[$paymentType][$RENT];
                                    if ($resVal == 0) {
                                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$RENT]) . '</b> không thể có '. $data['note'];
                                    }
                                }
                            } else {
                                $data['cssClass'] = 'alert-warning';
//                               $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['identity_code']['title']) . '</b> không tồn tại';
                                $dataWarning['empRent'][] = "Nhân sự thuê khoán: " . $data['emp_name'] . " - ID:" . $data['identity_code'] . " chưa tồn tại trong hệ thống.";
//                                dd($data);
                            }
                        } else {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : không tồn tại dữ liệu trong danh sách nhân sự thuê khoán';
                        }
                    } elseif ($data['emp_tax_code']) {
                        //TODO check mã số thuế
                        if (isset($employeeRentTax) && $employeeRentTax) {
                            $empRentTax = $employeeRentTax->firstWhere('emp_tax_code','===', $data['emp_tax_code']);
                            if ($empRentTax) {
                                if ($empRentTax->emp_name != $data['emp_name']) {
                                    $data['cssClass'] = 'error';
                                    $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_name']['title']) . '</b> so với mã số thuế không chính xác';
                                }
                                $data['employee_table'] = "employee_rent";

                                $paymentType = isset($validatePositionRule[$data['note']]) ? $data['note'] : "TKCM";
                                if (array_key_exists("phap_nhan", $r) && array_key_exists("note", $data)) {
                                    $resVal = $validatePositionRule[$paymentType][$RENT];
                                    if ($resVal == 0) {
                                        $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($validatePositionRule['title'][$RENT]) . '</b> không thể có '. $data['note'];
                                    }
                                }
                                $data['identity_code'] = $employeeRentTax->identity_code;
                            } else {
                                $data['cssClass'] = 'error';
                                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData['emp_tax_code']['title']) . '</b> không tồn tại';
                            }
                        } else {
                            $data['cssClass'] = 'error';
                            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : không tồn tại dữ liệu trong danh sách nhân sự thuê khoán';
                        }
                    }
                }

            } else {
                $r = $dataRequest;
            }
            $dataExcel = array_values($dataExcel);
            sort($dataError, SORT_NATURAL);

//        dd($dataExcel);
//        dd(microtime(true)-$start);
//        $dataError =
        } catch (PostTooLargeException $e) {
            $dataError[] = $e->getMessage();
        }
        return view('vouchers.import', [
            'request' => $r,
            'dataExcel' => $dataExcel,
            'dataError' => $dataError,
            'total' => $dataTotal,
            'dataWarning' => $dataWarning
        ]);
    }

    public function saveFileFTT(Request $request)
    {
        Topica::canOrRedirect('add.order');
        $data = $request->all();
        // check chứng từ trong tháng
//dd($data);
//        dd($ftt);
        DB::beginTransaction();
        try {
            $order = json_decode($data['order'], true);
//            dd($order);
            if (isset($data['phap_nhan']))
                $order['phap_nhan'] = $data['phap_nhan'];
            if (isset($data['month']))
                $order['month'] = $data['month'];
            if (isset($data['year']))
                $order['year'] = $data['year'];
            $dataTable = json_decode($data['dataExcel'], true);
            $isSalary = isset($order['view']) && $order['view'] == 'import-salary';
            if ($isSalary) {
                // trường hợp import bảng lương
                $order['isSalary'] = 1;
                $orderExist = $this->summaryRepository->getDataBy(['type' => SALARY, 'status' => 0, 'phap_nhan' => $order['phap_nhan'], 'month' => $order['month'], 'year' => $order['year']], false)->toArray();
                if ($orderExist) {
                    $check = $this->summaryRepository->delete(['type' => SALARY, 'status' => 0, 'phap_nhan' => $order['phap_nhan'], 'month' => $order['month'], 'year' => $order['year']]);
                }
                $ftt = $this->summaryRepository->getDataBy([
                    'ngay_thanh_toan' => [
                        'month' => $order['month'],
                        'year' => $order['year']
                    ],
                    'phap_nhan' => $order['phap_nhan'],
                    'notSalaryEmployee' => true
                ], false);
                foreach ($dataTable as &$t) {
//                dd($t);
                    if (!isset($t['sum_thu_nhap_truoc_thue']))
                        $t['sum_thu_nhap_truoc_thue'] = $t['tong_thu_nhap_truoc_thue'];
                    if (!isset($t['sum_non_tax']))
                        $t['sum_non_tax'] = $t['tong_non_tax'];
                    if (!isset($t['sum_tnct']))
                        $t['sum_tnct'] = $t['tong_tnct'];
                    if (!isset($t['sum_bhxh']))
                        $t['sum_bhxh'] = $t['bhxh'];
                    if (!isset($t['sum_thue_tam_trich']))
                        $t['sum_thue_tam_trich'] = $t['thue_tam_trich'];
                    if (!isset($t['sum_thuc_nhan']))
                        $t['sum_thuc_nhan'] = $t['thuc_nhan'];
                    $t['tong_thu_nhap_truoc_thue'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_thu_nhap_truoc_thue');
                    $t['tong_non_tax'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_non_tax');
                    $t['tong_tnct'] -= $ftt->where('employee_code', $t['employee_code'])->sum('tong_tnct');
                    $t['bhxh'] -= $ftt->where('employee_code', $t['employee_code'])->sum('bhxh');
                    $t['thue_tam_trich'] -= $ftt->where('employee_code', $t['employee_code'])->sum('thue_tam_trich');
                    $t['thuc_nhan'] -= $ftt->where('employee_code', $t['employee_code'])->sum('thuc_nhan');
                }
            } else {
                // trường hợp import chứng từ
                $order['isSalary'] = 0;
//                dd($dataTable);
                $dataEmpRent = [];
                foreach ($dataTable as &$e_order) {

                    if (!isset($e_order['employee_table'])) {
                        $e_order['employee_table'] = 'employee_rent';
                        $temp = [
                            'identity_code' => $e_order['identity_code'],
                            'identity_type' => (strlen($e_order['identity_code']) == 9 || strlen($e_order['identity_code']) == 12) ? 'cmt' : 'hc',
                            'emp_name' => $e_order['emp_name'],
                            'emp_live_status' => 1
                        ];
                        if (isset($e_order['identity_type']) && (strtolower($e_order['identity_type']) == 'cmt') || strtolower($e_order['identity_type']) == 'cmnd') {
                            $temp['identity_type'] = "cmt";
                        }
                        if (isset($e_order['identity_type']) && (str_slug($e_order['identity_type'], '-') == 'ho-chieu')) {
                            $temp['identity_type'] = "hc";
                        }
                        if (isset($e_order['emp_live_status']) && (str_slug($e_order['emp_live_status'], '-') == 'cu-tru')) {
                            $temp['emp_live_status'] = 1;
                        }
                        if (isset($e_order['emp_live_status']) && (str_slug($e_order['emp_live_status'], '-') == 'khong-cu-tru')) {
                            $temp['emp_live_status'] = 0;
                        }
                        if (isset($e_order['emp_country']) && $e_order['emp_country']) {
                            $temp['emp_country'] = $e_order['emp_country'];
                        }
                        $dataEmpRent[] = $temp;
                    }
                    if ($e_order['employee_table'] != 'employees' && !$e_order['employee_code']) {
                        $e_order['employee_code'] = $e_order['identity_code'];
                    }
                }
            }
//            dd($dataEmpRent);
            if (isset($dataEmpRent) && $dataEmpRent) {
                foreach ($dataEmpRent as $emp) {
                    if ($emp['identity_code'])
                        $this->empRentRepository->saveData($emp);
                }
            }
            $saveOrder = $this->orderRepository->saveData($order, $dataTable);
            if (!$saveOrder)
                throw new \Exception('Error');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
        $backToCrossCheck = $request->session()->get('backToCrossCheck');
        if (!$isSalary && !empty($backToCrossCheck)) {
            return redirect()->route("cross_check.pickOrders", [
                'order_id' => $saveOrder->id,
                'cross_check_info_id' => $backToCrossCheck['info_id']
            ]);
        }

        return redirect()->route('order.listOrders');
    }


    public function orderInfo(Request $request, $order_id)
    {
        $requestData = $request->all();
        $order = $this->orderRepository->getDataBy(['id' => $order_id])->first();
        Topica::canOrRedirect('edit.order', $order);
        $data = $this->orderRepository->findById($order_id);
        if (!$data) {
            if ($request->ajax()) // This is what i am needing.
            {
                return "Không tìm thấy bộ thanh toán";
            }
            return redirect()->route('order.listOrders');
        }
        return view('orders.info', compact('data', 'requestData'));
    }

//    public function editOrderInfo($order_id)
//    {
//        $data = $this->orderRepository->findById($order_id);
//        if (!$data) {
//            return redirect()->route('order.listOrders');
//        }
//        return view('orders.info', [
//            'data' => $data,
//            'type' => 'edit'
//        ]);
//    }

    public function updateOrderInfo(EditOrderRequest $request)
    {
        $data = $request->all();
//        unset($data['phap_nhan']);
        $order = $this->orderRepository->getDataBy(['id' => $data['id']])->first();
        Topica::canOrAbort('edit.order', $order);

        DB::beginTransaction();
        try {
            $order = $this->orderRepository->saveData($data);
            if (!$order) {
                throw new Exception('Error');
            }
            DB::commit();
            return redirect()->route('order.orderInfo', $data['id'])->with("message", [
                "title" => "Thành công",
                "content" => "Sửa thông tin bộ thanh toán thành công",
                "type" => "success"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('order.orderInfo', $data['id'])->with("message", [
                "title" => "Thất bại",
                "content" => $e->getMessage(),
                "type" => "danger"
            ]);
        }
        return redirect()->route('order.orderInfo', $data['id']);
    }

    public function deleteOrder(Request $request, $orderId)
    {
        $order = $this->orderRepository->getDataBy(['id' => $orderId])->first();
        Topica::canOrAbort('delete.order', $order);
        $data = $request->all();
        $result = $this->orderRepository->delete([
            "id" => $orderId
        ]);
        if (isset($data['cross_check_info_id'])) {
            if (isset($data['month'])) {
                if (is_array($result) && !$result['result']) {
                    return redirect()->route("cross_check.showByMonth", [
                        'thang' => $data['month'],
                        'nam' => $data['year'],
                        'phap_nhan' => $data['pn'],
                        'luong' => $data['luong'],
                    ])->with("message", [
                        "title" => "Thất bại",
                        "content" => $result['message'],
                        "type" => "danger"
                    ]);
                }

                return redirect()->route("cross_check.showByMonth", [
                    'thang' => $data['month'],
                    'nam' => $data['year'],
                    'phap_nhan' => $data['pn'],
                    'luong' => $data['luong'],
                ])->with("message", [
                    "title" => "Thành công",
                    "content" => "Hủy bỏ đối soát cho bộ F-" . $orderId . " thành công",
                    "type" => "success"
                ]);
            } else {
                if (is_array($result) && !$result['result']) {
                    return redirect()->route("cross_check.showByYear", [
                        'nam' => $data['year'],
                        'phap_nhan' => $data['pn'],
                    ])->with("message", [
                        "title" => "Thất bại",
                        "content" => $result['message'],
                        "type" => "danger"
                    ]);
                }

                return redirect()->route("cross_check.showByYear", [
                    'nam' => $data['year'],
                    'phap_nhan' => $data['pn'],
                ])->with("message", [
                    "title" => "Thành công",
                    "content" => "Hủy bỏ đối soát cho bộ F-" . $orderId . " thành công",
                    "type" => "success"
                ]);
            }
        }

        if (is_array($result) && !$result['result']) {
            return Redirect::back()->with("message", [
                "title" => "Thất bại",
                "content" => $result['message'],
                "type" => "danger"
            ]);
        }

        $cross = $this->crossCheckRepository->getDataBy([
            "order_id" => $orderId
        ], false);

        if (!$cross->isEmpty()) {
            foreach ($cross as $c) {
                $c->update([
                    "order_id" => null,
                    "temp_order" => 0
                ]);
            }
        }

        return Redirect::back()->with("message", [
            "title" => "Thành công",
            "content" => "Hủy bỏ đối soát cho bộ F-" . $orderId . " thành công",
            "type" => "success"
        ]);
    }
}