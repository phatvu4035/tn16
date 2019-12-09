<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Helpers\ImportExcel;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Models\OrderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportOldValueController extends Controller
{

    protected $importExcel;

    protected $employeeRepository;

    protected $empRentRepository;

    protected $typeRepository;

    protected $summaryRepository;

    protected $orderRepository;

    public function __construct(ImportExcel $importExcel, OrderRepositoryInterface $orderRepository, SummaryRepositoryInterface $summaryRepository, TypeRepositoryInterface $typeRepository, EmployeeRepositoryInterface $employeeRepository, EmpRentRepositoryInterface $empRentRepository)
    {
        $this->importExcel = $importExcel;
        $this->employeeRepository = $employeeRepository;
        $this->empRentRepository = $empRentRepository;
        $this->typeRepository = $typeRepository;
        $this->summaryRepository = $summaryRepository;
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        Topica::canOrRedirect('import.old-value');
        return view('import.old-value', [
            'request' => $data
        ]);
    }

    public function importOldSalary(Request $request)
    {
        set_time_limit(0);
        ini_set("memory_limit", -1);
        $data = $request->all();
        $dataEmployee = [];
        $dataWarning = [];
        $dataExcel = [];
        $dataImport = [];
        $isError = false;
        $dataTotalSummary = [

        ];
        $dataTotal = [
            'tong_tn_truoc_thue' => 0,
            'tong_non_tax' => 0,
            'tong_tnct' => 0,
            'bhxh' => 0,
            'thue_tam_trich' => 0,
            'thuc_nhan' => 0,
            'giam_tru_ban_than' => 0,
            'giam_tru_gia_canh' => 0
        ];

        Topica::canOrRedirect('import.old-value');

        if (isset($data['importFile'])) {
            $fileName = 'summary-' . $data['month'] . $data['year'] . '-' . $data['phap_nhan'] . '.xlsx';
            if ($fileName != $data['importFile']->getClientOriginalName()) {
                return view('import.old-value', [
                    'request' => $data,
                    'dataImport' => $dataImport,
                    'total' => $dataTotal,
                    'dataExcel' => $dataExcel
                ]);
            }
            $dataExcel = $this->importExcel->getData($data['importFile']);
//            dd($dataExcel);
        } else {
            if (isset($data['dataExcel']) && $data['dataExcel']) {
                $dataExcel = json_decode($data['dataExcel'], true);
            }
        }
        $type = $this->typeRepository->getDataBy([], false)->pluck('name')->toArray();
        if ($dataExcel) {
            foreach ($dataExcel as $k => &$v) {
                if ($k > 0) {
                    foreach ($v as &$v1) {
                        $v1 = trim($v1);
                    }
                    if (substr($v['id'], 0, 1) === '/') {
                        $v['id'] = substr($v['id'], 1);
                    }
                    if (substr($v['id'], 0, 1) === "'") {
                        $v['id'] = substr($v['id'], 1);
                    }
                    if (substr($v['id'], 0, 2) === "''") {
                        $v['id'] = substr($v['id'], 2);
                    }
					$v['id'] = str_replace(' ', '', $v['id']);
                    $v['note'] = trim($v['note']);
                    if (is_numeric(explode('_', $v['noi_dung'])[0])) {
                        $dataImport[$v['ref']]['serial'] = explode('_', $v['noi_dung'])[0];
                    } else {
                        $dataImport[$v['ref']]['serial'] = $v['ref'];
                    }
                    $dataImport[$v['ref']]['phap_nhan'] = $data['phap_nhan'];
                    $dataImport[$v['ref']]['month'] = $data['month'];
                    $dataImport[$v['ref']]['year'] = $data['year'];
                    $dataImport[$v['ref']]['noi_dung'] = $v['noi_dung'];
                    $dataImport[$v['ref']]['ma_so_thue'] = $v['ma_so_thue'];
                    $dataImport[$v['ref']]['ma_osscar'] = 'NA';
                    $dataImport[$v['ref']]['ma_du_toan'] = 'NA';
                    $dataImport[$v['ref']]['phong_ban'] = 'NA';
                    $dataImport[$v['ref']]['ngay_de_xuat'] = $data['year'] . '-' . $data['month'] . '-28 00:00:00';
                    $dataImport[$v['ref']]['nguoi_huong'] = 'NA';
                    if (!isset($dataImport[$v['ref']]['summary'])) {
                        $dataImport[$v['ref']]['so_tien'] = $v['thuc_nhan'];
                    } else {
                        $dataImport[$v['ref']]['so_tien'] += $v['thuc_nhan'];
                    }
                    $dataImport[$v['ref']]['loai_tien'] = 'VND';
                    $dataImport[$v['ref']]['ty_gia'] = 1;
                    $dataImport[$v['ref']]['quy_doi'] = $dataImport[$v['ref']]['so_tien'];
                    $dataImport[$v['ref']]['san_pham'] = 'NA';
                    $dataImport[$v['ref']]['status'] = 0;
                    if ($v['note'] == SALARY) {
                        $dataImport[$v['ref']]['month'] = $data['month'];
                        $dataImport[$v['ref']]['year'] = $data['year'];
                        $dataImport[$v['ref']]['isSalary'] = 1;
                    } else {
                        $dataImport[$v['ref']]['isSalary'] = 0;
                    }
                    $dataTotal['tong_tn_truoc_thue'] += $v['tong_tn_truoc_thue'];
                    $dataTotal['tong_non_tax'] += $v['tong_non_tax'];
                    $dataTotal['tong_tnct'] += $v['tong_tnct'];
                    $dataTotal['bhxh'] += $v['bhxh'];
                    $dataTotal['thue_tam_trich'] += $v['thue_tam_trich'];
                    $dataTotal['thuc_nhan'] += $v['thuc_nhan'];
                    $dataTotal['giam_tru_ban_than'] += $v['giam_tru_ban_than'];
                    $dataTotal['giam_tru_gia_canh'] += $v['giam_tru_gia_canh'];

                    $dataImport[$v['ref']]['summary'][] = $v;

//                    dd(in_array('Thưởng NV', $type));
                    if (!in_array(trim($v['note']), $type)) {
                        $isError = true;
                        $dataImport[$v['ref']]['error'][] = "Nhân viên: <b>" . $v['ten_nv'] . "</b> có stt: <b>" . $v['stt'] . "</b> có loại chứng từ <b>" . $v['note'] . "</b> không nằm trong danh sách chứng từ";
                    }
                    


                    if ($v['ma_nv']) {
                        $dataEmployee['employee_code'][$v['ma_nv'] . ''] = $v['ma_nv'] . '';
                    } elseif ($v['id']) {
                        $dataEmployee['id'][$v['id'] . ''] = $v['id'] . '';
                    } elseif ($v['ma_so_thue'])
                        $dataEmployee['ma_so_thue'][$v['ma_so_thue'] . ''] = $v['ma_so_thue'] . '';
                } else {
                    unset($dataExcel[$k]);
                }
            }

            if (isset($dataImport) && $dataImport) {
                // check employee in HR20
                $employee = $this->employeeRepository->getDataByEmployeeCodeAndCMT($dataEmployee);

                if ($employee) {
                    foreach ($dataImport as $k1 => &$v1) {
                        if (isset($v1['summary'])) {
                            foreach ($v1['summary'] as &$summary) {

                                if ($summary['ma_nv']) {
                                    $emp = $employee->firstWhere('employee_code', $summary['ma_nv'] . '');
                                    if ($emp) {
                                        if ($summary['ten_nv'] == "#ERROR!") {
                                            $summary['ten_nv'] = $emp->last_name . ' ' . $emp->first_name;
                                        }
                                        if (str_slug($emp->last_name . ' ' . $emp->first_name, '-') != str_slug($summary['ten_nv'], '-')) {
                                            $isError = true;
                                            $v1['error'][] = "Nhân viên <b>" . $summary['ten_nv'] . "</b> không khớp với mã nhân viên <b>" . $summary['ma_nv'] . "</b> Tên trên HR20 là: " . $emp->last_name . ' ' . $emp->first_name;;
                                        }
                                        $summary['employee_table'] = 'employees';
                                        $summary['month'] = $data['month'];
                                        $summary['year'] = $data['year'];
                                    } else {
                                        $isError = true;
                                        $v1['error'][] = "Nhân viên: <b>" . $summary['ten_nv'] . "</b> có mã nhân viên: <b>" . $summary['ma_nv'] . "</b> không tồn tại trong HR20";
                                    }
                                } elseif ($summary['id']) {
                                    $emp = $employee->firstWhere('cmt', '===', $summary['id']);
                                    if ($emp) {
                                        if (str_slug($emp->last_name . ' ' . $emp->first_name, '-') != str_slug($summary['ten_nv'], '-')) {
                                            $isError = true;
                                            $v1['error'][] = "Nhân viên <b>" . $summary['ten_nv'] . "</b> không khớp với ID <b>" . $summary['id'] . "</b> Tên trên HR20 là: " . $emp->last_name . ' ' . $emp->first_name;
                                        }
                                        $summary['ma_nv'] = $emp->employee_code;
                                        $summary['employee_table'] = 'employees';
                                        $summary['month'] = $data['month'];
                                        $summary['year'] = $data['year'];
                                    }
                                } elseif ($summary['ma_so_thue']) {
                                    $emp = $employee->firstWhere('mst', $summary['ma_so_thue']);
                                    if ($emp) {
                                        if (str_slug($emp->last_name . ' ' . $emp->first_name, '-') != str_slug($summary['ten_nv'], '-')) {
                                            $isError = true;
                                            $v1['error'][] = "Nhân viên <b>" . $summary['ten_nv'] . "</b> không khớp với MST <b>" . $summary['ma_so_thue'] . "</b> Tên trên HR20 là: " . $emp->last_name . ' ' . $emp->first_name;
                                        }
                                        $summary['ma_nv'] = $emp->employee_code;
                                        $summary['employee_table'] = 'employees';
                                        $summary['month'] = $data['month'];
                                        $summary['year'] = $data['year'];
                                    }
                                }
                                if (!isset($dataTotalSummary[$summary['ma_nv']])) {
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thu_nhap_truoc_thue'] = $summary['tong_tn_truoc_thue'] ? $summary['tong_tn_truoc_thue'] : 0;
                                    $dataTotalSummary[$summary['ma_nv']]['sum_non_tax'] = $summary['tong_non_tax'] ? $summary['tong_non_tax'] : 0;
                                    $dataTotalSummary[$summary['ma_nv']]['sum_tnct'] = $summary['tong_tnct'] ? $summary['tong_tnct'] : 0;
                                    $dataTotalSummary[$summary['ma_nv']]['sum_bhxh'] = $summary['bhxh'] ? $summary['bhxh'] : 0;
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thue_tam_trich'] = $summary['thue_tam_trich'] ? $summary['thue_tam_trich'] : 0;
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thuc_nhan'] = $summary['thuc_nhan'] ? $summary['thuc_nhan'] : 0;
                                } else {
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thu_nhap_truoc_thue'] += $summary['tong_tn_truoc_thue'];
                                    $dataTotalSummary[$summary['ma_nv']]['sum_non_tax'] += $summary['tong_non_tax'];
                                    $dataTotalSummary[$summary['ma_nv']]['sum_tnct'] += $summary['tong_tnct'];
                                    $dataTotalSummary[$summary['ma_nv']]['sum_bhxh'] += $summary['bhxh'];
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thue_tam_trich'] += $summary['thue_tam_trich'];
                                    $dataTotalSummary[$summary['ma_nv']]['sum_thuc_nhan'] += $summary['thuc_nhan'];
                                }
                            }
                        }
                    }
                }
                // check nhan su thue khoan
                $employeeRent = $this->empRentRepository->getDataByIdAndMst($dataEmployee);
                if ($employeeRent) {
                    foreach ($dataImport as $k1 => &$v1) {
                        if (isset($v1['summary'])) {
                            foreach ($v1['summary'] as &$summary) {
                                if (!$summary['ma_nv']) {
                                    if ($summary['id']) {
                                        $empRent = $employeeRent->firstWhere('identity_code', '===', $summary['id'] . '');

                                        if ($empRent) {
                                            if (str_slug($empRent->emp_name, '-') != str_slug($summary['ten_nv'], '-')) {
                                                $isError = true;
                                                $v1['error'][] = "Nhân viên <b>" . $summary['ten_nv'] . "</b> không khớp với ID: <b>" . $summary['id'] . "</b> Tên trên kho dữ liệu là: " . $empRent->emp_name;
                                            }
                                            $summary['id'] = $empRent->identity_code;
                                            $summary['employee_table'] = 'employee_rent';
                                            $summary['month'] = $data['month'];
                                            $summary['year'] = $data['year'];
                                        } else {
                                            $v1['warning'][] = "Nhân viên: <b>" . $summary['ten_nv'] . "</b> có ID: <b>" . $summary['id'] . "</b> chưa tồn tại trong hệ thống.";
                                            $summary['employee_table'] = 'employee_rent';
                                            $summary['month'] = $data['month'];
                                            $summary['year'] = $data['year'];
                                            $temp = [
                                                'identity_code' => $summary['id'],
                                                'identity_type' => (strlen($summary['id']) == 9 || strlen($summary['id']) == 12) ? 'cmt' : 'hc',
                                                'emp_name' => $summary['ten_nv'],
                                                'emp_live_status' => 1
                                            ];
                                            if (isset($summary['loai_the']) && ((strtolower($summary['loai_the']) == 'cmt') || strtolower($summary['loai_the']) == 'cmnd')) {
                                                $temp['identity_type'] = "cmt";
                                            }
                                            if (isset($summary['loai_the']) && (str_slug($summary['loai_the'], '-') == 'ho-chieu')) {
                                                $temp['identity_type'] = "hc";
                                            }
                                            if (isset($summary['tinh_trang_cu_tru']) && (str_slug($summary['tinh_trang_cu_tru'], '-') == 'cu-tru')) {
                                                $temp['emp_live_status'] = 1;
                                            }
                                            if (isset($summary['tinh_trang_cu_tru']) && (str_slug($summary['tinh_trang_cu_tru'], '-') == 'khong-cu-tru')) {
                                                $temp['emp_live_status'] = 0;
                                            }
                                            if (isset($summary['quoc_tich']) && $summary['quoc_tich']) {
                                                $temp['emp_country'] = $summary['emp_country'];
                                            }
                                            $dataEmpRent[] = $temp;
                                        }
                                    } elseif ($summary['ma_so_thue']) {
                                        $empRent = $employeeRent->firstWhere('emp_tax_code', '===', $summary['ma_so_thue']);
                                        if ($empRent) {
                                            if (str_slug($empRent->emp_name, '-') != str_slug($summary['ten_nv'], '-')) {
                                                $isError = true;
                                                $v1['error'][] = "Nhân viên <b>" . $summary['ten_nv'] . "</b> không khớp với Mã số thuế <b>" . $summary['id'] . "</b> Tên trên kho dữ liệu là: " . $empRent->emp_name;
                                            }
                                            $summary['id'] = $empRent->identity_code;
                                            $summary['employee_table'] = 'employee_rent';
                                            $summary['month'] = $data['month'];
                                            $summary['year'] = $data['year'];
                                        } else {
                                            $isError = true;
                                            $v1['error'][] = "Nhân viên: <b>" . $summary['ten_nv'] . "</b> có số thứ tự: " . $summary['stt'] . " không xác định";
                                        }
                                    } else {
                                        $isError = true;
                                        $v1['error'][] = "Nhân viên: <b>" . $summary['ten_nv'] . "</b> không tồn tại Mã NV, ID, MST";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        if ($isError || !(isset($data['isSave']) && $data['isSave'] == 'true')) {
            return view('import.old-value', [
                'request' => $data,
                'dataImport' => $dataImport,
                'total' => $dataTotal,
                'dataExcel' => $dataExcel
            ]);
        } else {
//            dd($data);
            $orderExistCheck = $this->summaryRepository->getDataBy(['status' => 1, 'phap_nhan' => $data['phap_nhan'], 'month' => $data['month'], 'year' => $data['year']], false)->toArray();
            if ($orderExistCheck) {
                return "Dữ liệu đã có chứng từ được đối soát, chức năng không thể sử dụng";
            }
//            $summaryExist = $this->summaryRepository->getDataBy(['status' => 0, 'phap_nhan' => $data['phap_nhan'], 'month' => $data['month'], 'year' => $data['year']], false)->toArray();
////            dd($summaryExist);
//            if ($summaryExist) {
//                $check = $this->summaryRepository->delete(['status' => 0, 'phap_nhan' => $data['phap_nhan'], 'month' => $data['month'], 'year' => $data['year']]);
//            }
//            $orderExist = $this->orderRepository->getDataBy(['status' => 0, 'phap_nhan' => $data['phap_nhan'], 'month' => $data['month'], 'year' => $data['year']], false)->pluck('id')->toArray();
//            if ($orderExist)
//                $this->orderRepository->delete(['id' => $orderExist]);


            DB::beginTransaction();
            try {
                foreach ($dataImport as $import) {
                    foreach ($import['summary'] as &$s) {
                        if (isset($s['employee_table'])) {
                            if ($s['employee_table'] == "employees") {
                                $s['employee_code'] = $s['ma_nv'];
                            } else {
                                $s['employee_code'] = $s['id'];
                            }
                            $s['tong_thu_nhap_truoc_thue'] = $s['tong_tn_truoc_thue'];
                            $s['tong_tn_truoc_thue'] = $s['tong_tn_truoc_thue'] ? $s['tong_tn_truoc_thue'] : 0;
                            $s['tong_non_tax'] = $s['tong_non_tax'] ? $s['tong_non_tax'] : 0;
                            $s['tong_tnct'] = $s['tong_tnct'] ? $s['tong_tn_truoc_thue'] : 0;
                            $s['bhxh'] = $s['bhxh'] ? $s['bhxh'] : 0;
                            $s['thue_tam_trich'] = $s['thue_tam_trich'] ? $s['thue_tam_trich'] : 0;
                            $s['thuc_nhan'] = $s['thuc_nhan'] ? $s['thuc_nhan'] : 0;
                            if ($s['note'] == SALARY) {
                                $s['sum_thu_nhap_truoc_thue'] = $s['tong_tn_truoc_thue'] ? $s['tong_tn_truoc_thue'] : 0;
                                $s['sum_non_tax'] = $s['tong_non_tax'] ? $s['tong_non_tax'] : 0;
                                $s['sum_tnct'] = $s['tong_tnct'] ? $s['tong_tn_truoc_thue'] : 0;
                                $s['sum_bhxh'] = $s['bhxh'] ? $s['bhxh'] : 0;
                                $s['sum_thue_tam_trich'] = $s['thue_tam_trich'] ? $s['thue_tam_trich'] : 0;
                                $s['sum_thuc_nhan'] = $s['thuc_nhan'] ? $s['thuc_nhan'] : 0;
                            }
                        } else {
                            dd($s);
                        }

                    }
                    $saveOrder = $this->orderRepository->saveData($import, $import['summary']);
                }
                if (isset($dataEmpRent) && $dataEmpRent) {
                    foreach ($dataEmpRent as $emp) {
                        if ($emp['identity_code'])
                            $this->empRentRepository->saveData($emp);
                    }
                }
                DB::commit();
                return redirect()->route('save.old.value.success');
            } catch (\Exception $e) {
                DB::rollback();
                dd($e->getMessage());
            }
        }
    }

}
