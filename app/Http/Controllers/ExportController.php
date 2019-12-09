<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Helpers\ExportExcel;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    protected $exportExcel;

    protected $summaryRepository;

    protected $employeeRepository;

    protected $typeRepository;

    public function __construct(ExportExcel $exportExcel, TypeRepositoryInterface $typeRepository, SummaryRepositoryInterface $summaryRepository, EmployeeRepositoryInterface $employeeRepository)
    {
        $this->exportExcel = $exportExcel;
        $this->summaryRepository = $summaryRepository;
        $this->employeeRepository = $employeeRepository;
        $this->typeRepository = $typeRepository;
    }

    public function export401()
    {
        Topica::canOrRedirect('export.401');
        return view('export.401');
    }

    public function export402()
    {
        Topica::canOrRedirect('export.402');
        return view('export.402');
    }

    public function export403()
    {
        Topica::canOrRedirect('export.402');
        return view('export.403');
    }

    public function getExport401(Request $request)
    {
        $start = microtime(true);
        Topica::canOrRedirect('export.401');
        //validate dữ liệu
        $validate = $this->validate($request, [
            'phap_nhan' => 'required',
            'month' => 'required',
            'year' => 'required|numeric|digits:4',
            'type_data' => 'required|numeric'
        ], [
            'phap_nhan.required' => "Pháp nhân không được để trống",
            'month.required' => "Tháng không được để trống",
            'year.required' => "Năm không được để trống",
            'year.numeric' => "Năm phải là số",
            'year.digits' => "Năm phải có 4 số",
            'type_data.required' => "Bạn phải chọn loại dữ liệu được tính",
            'type_data.numeric' => "Phải là số"
        ]);

        $data = $request->all();
        $message = [];

        try {
//            throw new \Exception('sssss');
//            $summary = $this->summaryRepository->getReportData([
//                'select' => "COUNT(DISTINCT summary.employee_code) AS tong_nv,
//SUM(case when type = 1 then sum_tnct else tong_tnct end) AS tong_tnct,
//COUNT(DISTINCT CASE WHEN (type != 1 and thue_tam_trich > 0) or (type =1 and sum_thue_tam_trich>0) THEN summary.employee_code ELSE NULL END) AS tong_nhan_su_nop_thue,
// SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN tong_tnct ELSE case when type=1 and sum_thue_tam_trich>0 then sum_tnct else 0 end END) AS tong_tnct_ns_nop_thue,
// SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN thue_tam_trich ELSE case when type = 1 and sum_thue_tam_trich>0 then sum_thue_tam_trich else 0 end END) AS thue_tncn,
// summary.type AS TYPE1,
// type.name AS name,
// employee_rent.emp_name AS emp_rent_name,
// employee_rent.emp_live_status AS live_status
//                            ",
//
//                'ngay_thanh_toan' => [
//                    'month' => $data['month'],
//                    'year' => $data['year'],
//                ],
//                'phap_nhan' => $data['phap_nhan'],
//                'group_by' => ['TYPE1', 'employee_rent.emp_live_status'],
//                'union_all' => [
//                    'select' => "
//                    COUNT(DISTINCT summary.employee_code) AS tong_nv, SUM(tong_tnct) AS tong_tnct, COUNT(DISTINCT CASE WHEN thue_tam_trich > 0 THEN summary.employee_code ELSE NULL END) AS tong_nhan_su_nop_thue, SUM(CASE WHEN thue_tam_trich > 0 THEN tong_tnct ELSE 0 END) AS tong_tnct_ns_nop_thue, SUM(CASE WHEN thue_tam_trich > 0 THEN thue_tam_trich ELSE 0 END) AS thue_tncn,
//CASE WHEN TYPE = 6 OR TYPE = 5 THEN \"com_thuong\" ELSE case when type = 2 or type =4 or type=11 then \"ctv\" else \"khac\" end END AS TYPE1,
// CASE WHEN TYPE = 6 OR TYPE = 5 THEN \"com_thuong\" ELSE case when type = 2 or type =4 or type=11 then \"ctv\" else \"khac\" end END AS name,
// employee_rent.emp_name AS emp_rent_name,
// employee_rent.emp_live_status AS live_status
//                    ",
//                    'group_by' => 'TYPE1'
//                ],
//                'union_all_1' => [
//                    'select' => "
//                    COUNT(DISTINCT summary.employee_code) AS tong_nv, SUM(case when type = 1 then sum_tnct else tong_tnct end) AS tong_tnct,
//COUNT(DISTINCT CASE WHEN (type != 1 and thue_tam_trich > 0) or (type =1 and sum_thue_tam_trich>0) THEN summary.employee_code ELSE NULL END) AS tong_nhan_su_nop_thue,
// SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN tong_tnct ELSE case when type=1 and sum_thue_tam_trich>0 then sum_tnct else 0 end END) AS tong_tnct_ns_nop_thue,
// SUM(CASE WHEN (type != 1 and thue_tam_trich > 0)  THEN thue_tam_trich ELSE case when type = 1 and sum_thue_tam_trich>0 then sum_thue_tam_trich else 0 end END) AS thue_tncn,
//CASE WHEN TYPE = 6 OR TYPE = 5 or type =1 or type=13 THEN \"tong_luong\" ELSE \"khac1\" END AS TYPE1,
// CASE WHEN TYPE = 6 OR TYPE = 5 or type =1 or type=13 THEN \"tong_luong\" ELSE \"khac1\" END AS name,
// employee_rent.emp_name AS emp_rent_name,
// employee_rent.emp_live_status AS live_status
//                    ",
//                    'group_by' => 'TYPE1'
//                ]
//            ], false);
//            $dataExport = [
//                'data' => $data,
//                'summary' => $summary,
//                'com' => $this->summaryRepository->get401ReportOfSalaryAndComBonus([
//                    'month' => $data['month'],
//                    'year' => $data['year'],
//                    'phap_nhan' => $data['phap_nhan'],
//                    'is_salary' => 0,
//                    'is_bonus' => 0,
//                    'is_com' => 1,
//                    'com_thuong' => 0
//                ]),
//                'bonus' => $this->summaryRepository->get401ReportOfSalaryAndComBonus([
//                    'month' => $data['month'],
//                    'year' => $data['year'],
//                    'phap_nhan' => $data['phap_nhan'],
//                    'is_salary' => 0,
//                    'is_bonus' => 1,
//                    'is_com' => 0,
//                    'com_thuong' => 0
//                ]),
//                'com_thuong' => $this->summaryRepository->get401ReportOfSalaryAndComBonus([
//                    'month' => $data['month'],
//                    'year' => $data['year'],
//                    'phap_nhan' => $data['phap_nhan'],
//                    'is_salary' => 0,
//                    'is_bonus' => 0,
//                    'is_com' => 0,
//                    'com_thuong' => 1
//                ])
//            ];
//            $this->exportExcel->export401($dataExport);
            $type = $this->typeRepository->getDataBy([], false)->pluck('name', 'id');
            if (isset($data['type_data']) && $data['type_data'] == '2') {
                $listSummary = $this->summaryRepository->getDataBy([
                    'select' => 'employee_code,phap_nhan,tong_thu_nhap_truoc_thue,tong_non_tax,tong_tnct,bhxh,thue_tam_trich,thuc_nhan,sum_thu_nhap_truoc_thue,sum_non_tax,sum_tnct,sum_bhxh,sum_thue_tam_trich,sum_thuc_nhan,type',
                    'ngay_thanh_toan' => [
                        'month' => $data['month'],
                        'year' => $data['year']
                    ],
                    'phap_nhan' => $data['phap_nhan']
                ], false)->toArray();
            } else {
                $listSummary = $this->summaryRepository->getDataBy(['select' => 'employee_code,phap_nhan,tong_thu_nhap_truoc_thue,tong_non_tax,tong_tnct,bhxh,thue_tam_trich,thuc_nhan,sum_thu_nhap_truoc_thue,sum_non_tax,sum_tnct,sum_bhxh,sum_thue_tam_trich,sum_thuc_nhan,type', 'month' => $data['month'], 'year' => $data['year'], 'phap_nhan' => $data['phap_nhan']], false)->toArray();

            }
            $dataEmployee = [];
//            $start = microtime(true);
            foreach ($listSummary as $key => $value) {
                if (isset($dataEmployee[$value['employee_code']])) {
                    $dataEmployee[$value['employee_code']]['count'] += 1;
                    $dataEmployee[$value['employee_code']]['tong_thu_nhap_truoc_thue'] += $value['tong_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']]['tong_non_tax'] += $value['tong_non_tax'];
                    $dataEmployee[$value['employee_code']]['tong_tnct'] += $value['tong_tnct'];
                    $dataEmployee[$value['employee_code']]['bhxh'] += $value['bhxh'];
                    $dataEmployee[$value['employee_code']]['thue_tam_trich'] += $value['thue_tam_trich'];
                    $dataEmployee[$value['employee_code']]['thuc_nhan'] += $value['thuc_nhan'];
                    $dataEmployee[$value['employee_code']]['sum_thu_nhap_truoc_thue'] += $value['sum_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']]['sum_non_tax'] += $value['sum_non_tax'];
                    $dataEmployee[$value['employee_code']]['sum_tnct'] += $value['sum_tnct'];
                    $dataEmployee[$value['employee_code']]['sum_bhxh'] += $value['sum_bhxh'];
                    $dataEmployee[$value['employee_code']]['sum_thue_tam_trich'] += $value['sum_thue_tam_trich'];
                    $dataEmployee[$value['employee_code']]['sum_thuc_nhan'] += $value['sum_thuc_nhan'];

                } else {
                    $dataEmployee[$value['employee_code']]['employee_code'] = $value['employee_code'];
                    $dataEmployee[$value['employee_code']]['count'] = 1;
                    $dataEmployee[$value['employee_code']]['tong_thu_nhap_truoc_thue'] = $value['tong_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']]['tong_non_tax'] = $value['tong_non_tax'];
                    $dataEmployee[$value['employee_code']]['tong_tnct'] = $value['tong_tnct'];
                    $dataEmployee[$value['employee_code']]['bhxh'] = $value['bhxh'];
                    $dataEmployee[$value['employee_code']]['thue_tam_trich'] = $value['thue_tam_trich'];
                    $dataEmployee[$value['employee_code']]['thuc_nhan'] = $value['thuc_nhan'];
                    $dataEmployee[$value['employee_code']]['sum_thu_nhap_truoc_thue'] = $value['sum_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']]['sum_non_tax'] = $value['sum_non_tax'];
                    $dataEmployee[$value['employee_code']]['sum_tnct'] = $value['sum_tnct'];
                    $dataEmployee[$value['employee_code']]['sum_bhxh'] = $value['sum_bhxh'];
                    $dataEmployee[$value['employee_code']]['sum_thue_tam_trich'] = $value['sum_thue_tam_trich'];
                    $dataEmployee[$value['employee_code']]['sum_thuc_nhan'] = $value['sum_thuc_nhan'];

                }


                $str = str_slug($type[$value['type']], '_');
                if (!isset($dataEmployee[$value['employee_code']][$str])) {
                    $dataEmployee[$value['employee_code']]['is_' . $str] = 1;
                    $dataEmployee[$value['employee_code']][$str]['count'] = 1;
                    $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] = $value['tong_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] = $value['tong_non_tax'];
                    $dataEmployee[$value['employee_code']][$str]['tong_tnct'] = $value['tong_tnct'];
                    $dataEmployee[$value['employee_code']][$str]['bhxh'] = $value['bhxh'];
                    $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] = $value['thue_tam_trich'];
                    $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] = $value['thuc_nhan'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] = $value['sum_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] = $value['sum_non_tax'];
                    $dataEmployee[$value['employee_code']][$str]['sum_tnct'] = $value['sum_tnct'];
                    $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] = $value['sum_bhxh'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] = $value['sum_thue_tam_trich'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] = $value['sum_thuc_nhan'];
                } else {
                    $dataEmployee[$value['employee_code']]['is_' . $str] += 1;
                    $dataEmployee[$value['employee_code']][$str]['count'] += 1;
                    $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] += $value['tong_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] += $value['tong_non_tax'];
                    $dataEmployee[$value['employee_code']][$str]['tong_tnct'] += $value['tong_tnct'];
                    $dataEmployee[$value['employee_code']][$str]['bhxh'] += $value['bhxh'];
                    $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] += $value['thue_tam_trich'];
                    $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] += $value['thuc_nhan'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] += $value['sum_thu_nhap_truoc_thue'];
                    $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] += $value['sum_non_tax'];
                    $dataEmployee[$value['employee_code']][$str]['sum_tnct'] += $value['sum_tnct'];
                    $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] += $value['sum_bhxh'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] += $value['sum_thue_tam_trich'];
                    $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] += $value['sum_thuc_nhan'];
                }
                if ($value['type'] == 5 or $value['type'] == 6) {
                    $str = "com_thuong";
                    if (!isset($dataEmployee[$value['employee_code']][$str])) {
                        $dataEmployee[$value['employee_code']]['is_' . $str] = 1;
                        $dataEmployee[$value['employee_code']][$str]['count'] = 1;
                        $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] = $value['tong_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] = $value['tong_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['tong_tnct'] = $value['tong_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['bhxh'] = $value['bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] = $value['thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] = $value['thuc_nhan'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] = $value['sum_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] = $value['sum_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['sum_tnct'] = $value['sum_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] = $value['sum_bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] = $value['sum_thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] = $value['sum_thuc_nhan'];
                    } else {
                        $dataEmployee[$value['employee_code']]['is_' . $str] += 1;
                        $dataEmployee[$value['employee_code']][$str]['count'] += 1;
                        $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] += $value['tong_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] += $value['tong_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['tong_tnct'] += $value['tong_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['bhxh'] += $value['bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] += $value['thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] += $value['thuc_nhan'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] += $value['sum_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] += $value['sum_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['sum_tnct'] += $value['sum_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] += $value['sum_bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] += $value['sum_thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] += $value['sum_thuc_nhan'];
                    }
                }
                if ($value['type'] == 2 or $value['type'] == 4 or $value['type'] == 11) {
                    $str = "ctv";
                    if (!isset($dataEmployee[$value['employee_code']][$str])) {
                        $dataEmployee[$value['employee_code']]['is_' . $str] = 1;
                        $dataEmployee[$value['employee_code']][$str]['count'] = 1;
                        $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] = $value['tong_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] = $value['tong_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['tong_tnct'] = $value['tong_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['bhxh'] = $value['bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] = $value['thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] = $value['thuc_nhan'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] = $value['sum_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] = $value['sum_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['sum_tnct'] = $value['sum_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] = $value['sum_bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] = $value['sum_thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] = $value['sum_thuc_nhan'];
                    } else {
                        $dataEmployee[$value['employee_code']]['is_' . $str] += 1;
                        $dataEmployee[$value['employee_code']][$str]['count'] += 1;
                        $dataEmployee[$value['employee_code']][$str]['tong_thu_nhap_truoc_thue'] += $value['tong_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['tong_non_tax'] += $value['tong_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['tong_tnct'] += $value['tong_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['bhxh'] += $value['bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['thue_tam_trich'] += $value['thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['thuc_nhan'] += $value['thuc_nhan'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thu_nhap_truoc_thue'] += $value['sum_thu_nhap_truoc_thue'];
                        $dataEmployee[$value['employee_code']][$str]['sum_non_tax'] += $value['sum_non_tax'];
                        $dataEmployee[$value['employee_code']][$str]['sum_tnct'] += $value['sum_tnct'];
                        $dataEmployee[$value['employee_code']][$str]['sum_bhxh'] += $value['sum_bhxh'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thue_tam_trich'] += $value['sum_thue_tam_trich'];
                        $dataEmployee[$value['employee_code']][$str]['sum_thuc_nhan'] += $value['sum_thuc_nhan'];
                    }
                }

            }
//            dd($dataEmployee);
            $report = [
                'lai_vay' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'luong_nv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'com_thuong' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'thuong_nv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'com_nv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'gvnn_co_hdld' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'luong_ctv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'com_ctv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'thuong_ctv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_viet_nam' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'tkcm' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'ctv' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'chia_se_doanh_thu' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_au_my_ko_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_au_my_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_au_my' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_thai_lan' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_thai_lan_ko_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_thai_lan_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_philipine' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_philipine_ko_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
                'giang_vien_philipine_cu_tru' => [
                    'tong_nv' => 0,
                    'tong_tnct_cho_ca_nhan' => 0,
                    'tong_nv_chiu_thue' => 0,
                    'tong_tnct_cho_nv_nop_thue' => 0,
                    'thue_tncn' => 0
                ],
            ];
            $dataTest = [];
            foreach ($dataEmployee as $dEmployee) {
//                if (isset($dEmployee['is_lai_vay']) && $dEmployee['is_lai_vay'] > 0) {
//                    $report['lai_vay']['tong_nv'] += 1;
//                    $report['lai_vay']['tong_tnct_cho_ca_nhan'] += $dEmployee['tong_tnct'];
//                    if ($dEmployee['thue_tam_trich'] > 0) {
//                        $report['lai_vay']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['tong_tnct'];
//                        $report['lai_vay']['tong_nv_chiu_thue'] += 1;
//                    }
//                    $report['lai_vay']['thue_tncn'] += $dEmployee['thue_tam_trich'];
//                }
                if (isset($dEmployee['is_luong_nv']) && $dEmployee['is_luong_nv'] > 0) {
                    if (isset($data['type_data']) && $data['type_data'] == 2) {
                        $report['luong_nv']['tong_nv'] += 1;
                        $report['luong_nv']['tong_tnct_cho_ca_nhan'] += $dEmployee['tong_tnct'];
                        if ($dEmployee['thue_tam_trich'] > 0) {
                            $report['luong_nv']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['tong_tnct'];
                            $report['luong_nv']['tong_nv_chiu_thue'] += 1;
                        }
                        $report['luong_nv']['thue_tncn'] += $dEmployee['thue_tam_trich'];


                    } else {
                        $report['luong_nv']['tong_nv'] += 1;
                        $report['luong_nv']['tong_tnct_cho_ca_nhan'] += $dEmployee['luong_nv']['sum_tnct'];
                        if ($dEmployee['sum_thue_tam_trich'] > 0) {
                            $report['luong_nv']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['luong_nv']['sum_tnct'];
                            $report['luong_nv']['tong_nv_chiu_thue'] += 1;
                        }
                        $report['luong_nv']['thue_tncn'] += $dEmployee['luong_nv']['sum_thue_tam_trich'];
                    }
                } else {
//                    if (isset($dEmployee['is_com_nv']) && $dEmployee['is_com_nv'] > 0) {
//                        $dataTest[] = $dEmployee;
//                    }
                    $this->getDataChungTu(['lai_vay', 'com_thuong', 'thuong_nv', 'com_nv', 'gvnn_co_hdld', 'luong_ctv', 'com_ctv', 'thuong_ctv', 'giang_vien_viet_nam', 'tkcm', 'ctv', 'chia_se_doanh_thu'], $dEmployee, $report);
                    $this->getDataChungTuGVNN(['giang_vien_au_my', 'giang_vien_thai_lan', 'giang_vien_philipine'], $dEmployee, $report);
//                    if (isset($dEmployee['is_giang_vien_au_my']) && $dEmployee['is_giang_vien_au_my'] > 0) {
//                        $tyLeThue = round($dEmployee['giang_vien_au_my']['thue_tam_trich'] / $dEmployee['giang_vien_au_my']['tong_tnct'], 2);
//                        if ($tyLeThue == 0.2) {
//                            $report['giang_vien_au_my_ko_cu_tru']['tong_nv'] += 1;
//                            $report['giang_vien_au_my_ko_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                            if ($dEmployee['giang_vien_au_my']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_au_my_ko_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                                $report['giang_vien_au_my_ko_cu_tru']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_au_my_ko_cu_tru']['thue_tncn'] += $dEmployee['giang_vien_au_my']['thue_tam_trich'];
//                        } elseif ($tyLeThue == 0.1) {
//                            $report['giang_vien_au_my_cu_tru']['tong_nv'] += 1;
//                            $report['giang_vien_au_my_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                            if ($dEmployee['giang_vien_au_my']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_au_my_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                                $report['giang_vien_au_my_cu_tru']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_au_my_cu_tru']['thue_tncn'] += $dEmployee['giang_vien_au_my']['thue_tam_trich'];
//                        } else {
//                            $report['giang_vien_au_my']['tong_nv'] += 1;
//                            $report['giang_vien_au_my']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                            if ($dEmployee['giang_vien_au_my']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_au_my']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_au_my']['tong_tnct'];
//                                $report['giang_vien_au_my']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_au_my']['thue_tncn'] += $dEmployee['giang_vien_au_my']['thue_tam_trich'];
//                        }
//
//                    }
//                    if (isset($dEmployee['is_giang_vien_thai_lan']) && $dEmployee['is_giang_vien_thai_lan'] > 0) {
//                        $tyLeThue = round($dEmployee['giang_vien_thai_lan']['thue_tam_trich'] / $dEmployee['giang_vien_thai_lan']['tong_tnct'], 2);
//                        if ($tyLeThue == 0.2) {
//                            $report['giang_vien_thai_lan_ko_cu_tru']['tong_nv'] += 1;
//                            $report['giang_vien_thai_lan_ko_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                            if ($dEmployee['giang_vien_thai_lan']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_thai_lan_ko_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                                $report['giang_vien_thai_lan_ko_cu_tru']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_thai_lan_ko_cu_tru']['thue_tncn'] += $dEmployee['giang_vien_thai_lan']['thue_tam_trich'];
//                        } elseif ($tyLeThue == 0.1) {
//                            $report['giang_vien_thai_lan_cu_tru']['tong_nv'] += 1;
//                            $report['giang_vien_thai_lan_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                            if ($dEmployee['giang_vien_thai_lan']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_thai_lan_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                                $report['giang_vien_thai_lan_cu_tru']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_thai_lan_cu_tru']['thue_tncn'] += $dEmployee['giang_vien_thai_lan']['thue_tam_trich'];
//                        } else {
//                            $report['giang_vien_thai_lan']['tong_nv'] += 1;
//                            $report['giang_vien_thai_lan']['tong_tnct_cho_ca_nhan'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                            if ($dEmployee['giang_vien_thai_lan']['thue_tam_trich'] > 0) {
//                                $report['giang_vien_thai_lan']['tong_tnct_cho_nv_nop_thue'] += $dEmployee['giang_vien_thai_lan']['tong_tnct'];
//                                $report['giang_vien_thai_lan']['tong_nv_chiu_thue'] += 1;
//                            }
//                            $report['giang_vien_thai_lan']['thue_tncn'] += $dEmployee['giang_vien_thai_lan']['thue_tam_trich'];
//                        }
//                    }
                }

            }
//            dd($dataTest);
//            Excel::create('New file', function ($excel) use ($dataTest) {
//
//                $excel->sheet('New sheet', function ($sheet) use ($dataTest) {
//
//                    $sheet->loadView('export.components.table401', array('key' => $dataTest));
//
//                });
//
//            })->download();
            $dataExport = [
                'data' => $data,
                'summary' => $report
            ];
            $this->exportExcel->export401($dataExport);
            $message = [
                'title' => "Thành công",
                'content' => 'Lấy dữ liệu thành công',
                'type' => 'success'
            ];
        } catch (\Exception $e) {
            dd($e->getMessage());
            $message = [
                'title' => "Lỗi",
                'content' => 'Có lỗi xảy ra',
                'type' => 'danger'
            ];
        }
        $request->session()->flash('message', $message);
        return view('export.401', compact('data'));
    }

    private function getDataChungTu($array, $employee, &$report)
    {
        foreach ($array as $arr) {
            if (isset($employee['is_' . $arr]) && $employee['is_' . $arr] > 0) {
                $report[$arr]['tong_nv'] += 1;
                $report[$arr]['tong_tnct_cho_ca_nhan'] += $employee[$arr]['tong_tnct'];
                if ($employee[$arr]['thue_tam_trich'] > 0) {
                    $report[$arr]['tong_tnct_cho_nv_nop_thue'] += $employee[$arr]['tong_tnct'];
                    $report[$arr]['tong_nv_chiu_thue'] += 1;
                }
                $report[$arr]['thue_tncn'] += $employee[$arr]['thue_tam_trich'];
            }
        }
    }

    private function getDataChungTuGVNN($array, $dEmployee, &$report)
    {
        foreach ($array as $arr) {
            if (isset($dEmployee['is_' . $arr]) && $dEmployee['is_' . $arr] > 0) {
                $tyLeThue = round($dEmployee[$arr]['thue_tam_trich'] / $dEmployee[$arr]['tong_tnct'], 2);
                if ($tyLeThue == 0.2) {
                    $report[$arr . '_ko_cu_tru']['tong_nv'] += 1;
                    $report[$arr . '_ko_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee[$arr]['tong_tnct'];
                    if ($dEmployee[$arr]['thue_tam_trich'] > 0) {
                        $report[$arr . '_ko_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee[$arr]['tong_tnct'];
                        $report[$arr . '_ko_cu_tru']['tong_nv_chiu_thue'] += 1;
                    }
                    $report[$arr . '_ko_cu_tru']['thue_tncn'] += $dEmployee[$arr]['thue_tam_trich'];
                } elseif ($tyLeThue == 0.1) {
                    $report[$arr . '_cu_tru']['tong_nv'] += 1;
                    $report[$arr . '_cu_tru']['tong_tnct_cho_ca_nhan'] += $dEmployee[$arr]['tong_tnct'];
                    if ($dEmployee[$arr]['thue_tam_trich'] > 0) {
                        $report[$arr . '_cu_tru']['tong_tnct_cho_nv_nop_thue'] += $dEmployee[$arr]['tong_tnct'];
                        $report[$arr . '_cu_tru']['tong_nv_chiu_thue'] += 1;
                    }
                    $report[$arr . '_cu_tru']['thue_tncn'] += $dEmployee[$arr]['thue_tam_trich'];
                } else {
                    $report[$arr]['tong_nv'] += 1;
                    $report[$arr]['tong_tnct_cho_ca_nhan'] += $dEmployee[$arr]['tong_tnct'];
                    if ($dEmployee[$arr]['thue_tam_trich'] > 0) {
                        $report[$arr]['tong_tnct_cho_nv_nop_thue'] += $dEmployee[$arr]['tong_tnct'];
                        $report[$arr]['tong_nv_chiu_thue'] += 1;
                    }
                    $report[$arr]['thue_tncn'] += $dEmployee[$arr]['thue_tam_trich'];
                }
            }
        }
    }

    public function getExport402(Request $request)
    {
        Topica::canOrRedirect('export.402');
        //validate dữ liệu
        $validate = $this->validate($request, [
            'phap_nhan' => 'required',
            'year' => 'required|numeric|digits:4'
        ], [
            'phap_nhan.required' => "Pháp nhân không được để trống",
            'year.required' => "Năm không được để trống",
            'year.numeric' => "Năm phải là số",
            'year.digits' => "Năm phải có 4 số"
        ]);

        $data = $request->all();
        $message = [];

        try {
//            throw new \Exception('sssss');
            $summary = $this->employeeRepository->getReportData402($data);
            $dataExport = [
                'data' => $data,
                'summary' => $summary
            ];
            $this->exportExcel->export402($dataExport);
            $message = [
                'title' => "Thành công",
                'content' => 'Lấy dữ liệu thành công',
                'type' => 'success'
            ];
        } catch (\Exception $e) {
            $message = [
                'title' => "Lỗi",
                'content' => 'Có lỗi xảy ra',
                'type' => 'danger'
            ];
        }
        $request->session()->flash('message', $message);
        return view('export.402', compact('data'));
    }

    public function getExport403(Request $request)
    {
        Topica::canOrRedirect('export.403');
        //validate dữ liệu
        $validate = $this->validate($request, [
            'phap_nhan' => 'required',
            'year' => 'required|numeric|digits:4'
        ], [
            'phap_nhan.required' => "Pháp nhân không được để trống",
            'year.required' => "Năm không được để trống",
            'year.numeric' => "Năm phải là số",
            'year.digits' => "Năm phải có 4 số"
        ]);

        $data = $request->all();
        $message = [];

        try {
//            throw new \Exception('sssss');
            $summary = $this->summaryRepository->getReportData403($data);
            $dataExport = [
                'data' => $data,
                'summary' => $summary
            ];
            $this->exportExcel->export403($dataExport);
            $message = [
                'title' => "Thành công",
                'content' => 'Lấy dữ liệu thành công',
                'type' => 'success'
            ];
        } catch (\Exception $e) {
            $message = [
                'title' => "Lỗi",
                'content' => 'Có lỗi xảy ra',
                'type' => 'danger'
            ];
        }
        $request->session()->flash('message', $message);
        return view('export.403', compact('data'));
    }

    public function getExportO1(Request $request)
    {
        //validate dữ liệu
        $validate = $this->validate($request, [
            'phap_nhan' => 'required',
            'year' => 'required|numeric|digits:4'
        ], [
            'phap_nhan.required' => "Pháp nhân không được để trống",
            'year.required' => "Năm không được để trống",
            'year.numeric' => "Năm phải là số",
            'year.digits' => "Năm phải có 4 số"
        ]);

        $data = $request->all();
        $message = [];

        try {
            $summary = $this->employeeRepository->getReportDataO1($data);
            $dataExport = [
                'data' => $data,
                'summary' => $summary
            ];
            $this->exportExcel->exportO1($dataExport);
            $message = [
                'title' => "Thành công",
                'content' => 'Lấy dữ liệu thành công',
                'type' => 'success'
            ];
        } catch (\Exception $e) {
            $message = [
                'title' => "Lỗi",
                'content' => 'Có lỗi xảy ra',
                'type' => 'danger'
            ];
        }
        $request->session()->flash('message', $message);
        return redirect()->route('cross_check.showByMonth', [$data['phap_nhan'], $data['month'], $data['year']]);
    }
}
