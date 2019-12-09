<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 7/5/18
 * Time: 10:10 AM
 */

namespace App\Http\Controllers\Api;


use App\Facades\Topica;
use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Http\Repositories\Contracts\PTRepositoryInterface;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\EmployeeRent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    protected $summaryRepository;

    protected $employeeRepository;

    protected $ptRepository;

    protected $orderRepository;

    protected $typeRepository;

    protected $empRentRepository;

    protected $empRepository;

    public function __construct(SummaryRepositoryInterface $summaryRepository, EmployeeRepositoryInterface $employeeRepository, PTRepositoryInterface $ptRepository, OrderRepositoryInterface $orderRepository, TypeRepositoryInterface $typeRepository, EmpRentRepositoryInterface $empRentRepository)
    {
        $this->summaryRepository = $summaryRepository;
        $this->employeeRepository = $employeeRepository;
        $this->ptRepository = $ptRepository;
        $this->orderRepository = $orderRepository;
        $this->typeRepository = $typeRepository;
        $this->empRentRepository = $empRentRepository;
    }

    public function getTNEmployee(Request $request)
    {
        $getData = $request->all();
        $dataIsAdd = [];
        $header = getListTableSummaryTNCN();

        //bỏ mã số thuế
        unset($header['ma_so_thue']);

        $employee = $this->employeeRepository->getDataBy(['email' => Auth::user()->email])->first();
        if (Topica::can('Administrator123')) {
            if (isset($getData['employee_code'])) {
                $employee = $this->employeeRepository->getDataBy(['employee_code' => $getData['employee_code']])->first();
            }
        }

        if ($employee) {
            $getData['employee'] = $employee;
            $getData['select'] = "employee_code,
employee_table,
phap_nhan,
san_pham,
ma_so_thue,
SUM(case when type = 1 then sum_thu_nhap_truoc_thue else 0 end) AS sum_thu_nhap_truoc_thue,
SUM(case when type = 1 then sum_non_tax else 0 end) AS sum_non_tax,
SUM(case when type = 1 then sum_tnct else 0 end) AS sum_tnct,
SUM(case when type = 1 then sum_bhxh else 0 end) AS sum_bhxh,
SUM(case when type = 1 then sum_thue_tam_trich else 0 end) AS sum_thue_tam_trich,
SUM(case when type = 1 then sum_thuc_nhan else 0 end) AS sum_thuc_nhan,
SUM(case when type = 1 then giam_tru_ban_than else 0 end) AS sum_giam_tru_ban_than,
SUM(case when type = 1 then giam_tru_gia_canh else 0 end) AS sum_giam_tru_gia_canh,
SUM(case when status = 1 then tong_thu_nhap_truoc_thue else 0 end) AS tong_thu_nhap_truoc_thue,
SUM(case when status = 1 then tong_non_tax else 0 end) AS tong_non_tax,
SUM(case when status = 1 then tong_tnct else 0 end) AS tong_tnct,
SUM(case when status = 1 then bhxh else 0 end) AS bhxh,
SUM(case when status = 1 then thue_tam_trich else 0 end) AS thue_tam_trich,
SUM(case when status = 1 then thuc_nhan else 0 end) AS thuc_nhan,
SUM(case when status = 1 then giam_tru_ban_than else 0 end) AS giam_tru_ban_than,
SUM(case when status = 1 then giam_tru_gia_canh else 0 end) AS giam_tru_gia_canh,
SUM(case when status = 0 then tong_thu_nhap_truoc_thue else 0 end) AS tong_thu_nhap_truoc_thue_0ds,
SUM(case when status = 0 then tong_non_tax else 0 end) AS tong_non_tax_0ds,
SUM(case when status = 0 then tong_tnct else 0 end) AS tong_tnct_0ds,
SUM(case when status = 0 then bhxh else 0 end) AS bhxh_0ds,
SUM(case when status = 0 then thue_tam_trich else 0 end) AS thue_tam_trich_0ds,
SUM(case when status = 0 then thuc_nhan else 0 end) AS thuc_nhan_0ds,
SUM(case when status = 0 then giam_tru_ban_than else 0 end) AS giam_tru_ban_than_0ds,
SUM(case when status = 0 then giam_tru_gia_canh else 0 end) AS giam_tru_gia_canh_0ds,
SUM(tong_thu_nhap_truoc_thue) AS tong_thu_nhap_truoc_thue_all,
SUM(tong_non_tax) AS tong_non_tax_all,
SUM(tong_tnct) AS tong_tnct_all,
SUM(bhxh) AS bhxh_all,
SUM(thue_tam_trich) AS thue_tam_trich_all,
SUM(thuc_nhan) AS thuc_nhan_all,
SUM(giam_tru_ban_than) AS giam_tru_ban_than_all,
SUM(giam_tru_gia_canh) AS giam_tru_gia_canh_all,
sum(case when type = 1 then 1 else 0 end) as is_salary,
sum(case when status = 1 then 1 else 0 end) as is_status,
sum(case when type =1 and status = 1 then 1 else 0 end) as is_status_salary,
case when status = 1 then summary.month else case when summary.month is null then month(summary.created_at) else summary.month end end as month1,
case when status = 1 then summary.year else case when summary.year is null then year(summary.created_at) else summary.year end end as year1,
case when status = 1 then concat((case when summary.month < 10 then concat('0',summary.month) else summary.month end ),'/',summary.year) else case when summary.month is null and summary.year is null then DATE_FORMAT(`created_at`,'%m/%Y') else concat((case when summary.month < 10 then concat('0',summary.month) else summary.month end ),'/',summary.year) end end AS month_year,
data";
            $getData['group_by'] = ['employee_code', 'month1', 'year1', 'phap_nhan'];
            $getData['order_by_raw'] = "case when status = 1 then concat((case when summary.month < 10 then concat('0',summary.month) else summary.month end ),'/',summary.year) else case when summary.month is null and summary.year is null then DATE_FORMAT(`created_at`,'%m/%Y') else concat((case when summary.month < 10 then concat('0',summary.month) else summary.month end ),'/',summary.year) end end DESC";
            $getData['status'] = "all";
            $getData['is_additional_order'] = 'IsNotAddOrder';
            $data = $this->summaryRepository->getDataOfEmployee($getData, false);
//            dd($data);
            //get chung tu bo sung
//            $getData['select'] = "employee_code,
//employee_table,
//phap_nhan,
//san_pham,
//ma_so_thue,
//SUM(case when type = 1 then sum_thu_nhap_truoc_thue else 0 end) AS sum_thu_nhap_truoc_thue,
//SUM(case when type = 1 then sum_non_tax else 0 end) AS sum_non_tax,
//SUM(case when type = 1 then sum_tnct else 0 end) AS sum_tnct,
//SUM(case when type = 1 then sum_bhxh else 0 end) AS sum_bhxh,
//SUM(case when type = 1 then sum_thue_tam_trich else 0 end) AS sum_thue_tam_trich,
//SUM(case when type = 1 then sum_thuc_nhan else 0 end) AS sum_thuc_nhan,
//SUM(case when type = 1 then giam_tru_ban_than else 0 end) AS sum_giam_tru_ban_than,
//SUM(case when type = 1 then giam_tru_gia_canh else 0 end) AS sum_giam_tru_gia_canh,
//SUM(case when status = 1 then tong_thu_nhap_truoc_thue else 0 end) AS tong_thu_nhap_truoc_thue,
//SUM(case when status = 1 then tong_non_tax else 0 end) AS tong_non_tax,
//SUM(case when status = 1 then tong_tnct else 0 end) AS tong_tnct,
//SUM(case when status = 1 then bhxh else 0 end) AS bhxh,
//SUM(case when status = 1 then thue_tam_trich else 0 end) AS thue_tam_trich,
//SUM(case when status = 1 then thuc_nhan else 0 end) AS thuc_nhan,
//SUM(case when status = 1 then giam_tru_ban_than else 0 end) AS giam_tru_ban_than,
//SUM(case when status = 1 then giam_tru_gia_canh else 0 end) AS giam_tru_gia_canh,
//SUM(case when status = 0 then tong_thu_nhap_truoc_thue else 0 end) AS tong_thu_nhap_truoc_thue_0ds,
//SUM(case when status = 0 then tong_non_tax else 0 end) AS tong_non_tax_0ds,
//SUM(case when status = 0 then tong_tnct else 0 end) AS tong_tnct_0ds,
//SUM(case when status = 0 then bhxh else 0 end) AS bhxh_0ds,
//SUM(case when status = 0 then thue_tam_trich else 0 end) AS thue_tam_trich_0ds,
//SUM(case when status = 0 then thuc_nhan else 0 end) AS thuc_nhan_0ds,
//SUM(case when status = 0 then giam_tru_ban_than else 0 end) AS giam_tru_ban_than_0ds,
//SUM(case when status = 0 then giam_tru_gia_canh else 0 end) AS giam_tru_gia_canh_0ds,
//SUM(tong_thu_nhap_truoc_thue) AS tong_thu_nhap_truoc_thue_all,
//SUM(tong_non_tax) AS tong_non_tax_all,
//SUM(tong_tnct) AS tong_tnct_all,
//SUM(bhxh) AS bhxh_all,
//SUM(thue_tam_trich) AS thue_tam_trich_all,
//SUM(thuc_nhan) AS thuc_nhan_all,
//SUM(giam_tru_ban_than) AS giam_tru_ban_than_all,
//SUM(giam_tru_gia_canh) AS giam_tru_gia_canh_all,
//sum(case when type = 1 then 1 else 0 end) as is_salary,
//sum(case when status = 1 then 1 else 0 end) as is_status,
//case when status = 1 then month(summary.ngay_thanh_toan) else month(summary.ngay_than_toan) end as month1,
//case when status = 1 then year(summary.ngay_thanh_toan) else year(summary.created_at) end as year1,
//case when status = 1 then DATE_FORMAT(`ngay_thanh_toan`,'%m/%Y') else DATE_FORMAT(`created_at`,'%m/%Y') end AS month_year,
//data";
            $getData['is_additional_order'] = 'IsAddOrder';
            $dataIsAdd = $this->summaryRepository->getDataOfEmployee($getData, false);
        } else {
            $data = [];
            $getData['employee'] = 1;
        }
        $dataNotStatus = [];
        $report = [[
            'tong_tnct' => 0,
            'tong_thu_nhap_truoc_thue' => 0,
            'tong_non_tax' => 0,
            'tong_tnct' => 0,
            'bhxh' => 0,
            'thue_tam_trich' => 0,
            'thuc_nhan' => 0,
            'giam_tru_ban_than' => 0,
            'giam_tru_gia_canh' => 0
        ]];
        $reportIsAdd = [[
            'tong_tnct' => 0,
            'tong_thu_nhap_truoc_thue' => 0,
            'tong_non_tax' => 0,
            'tong_tnct' => 0,
            'bhxh' => 0,
            'thue_tam_trich' => 0,
            'thuc_nhan' => 0,
            'giam_tru_ban_than' => 0,
            'giam_tru_gia_canh' => 0
        ]];

        if ($data) {
            foreach ($data as &$d) {
                if ($d->is_salary > 0) {
                    $d->tong_thu_nhap_truoc_thue = $d->sum_thu_nhap_truoc_thue;
                    $d->tong_non_tax = $d->sum_non_tax;
                    $d->tong_tnct = $d->sum_tnct;
                    $d->bhxh = $d->sum_bhxh;
                    $d->thue_tam_trich = $d->sum_thue_tam_trich;
                    $d->thuc_nhan = $d->sum_thuc_nhan;
                    $d->giam_tru_ban_than = $d->sum_giam_tru_ban_than;
                    $d->giam_tru_gia_canh = $d->sum_giam_tru_gia_canh;
                } else {
                    if ($d->is_status == 0) {
                        $d->tong_thu_nhap_truoc_thue = $d->tong_thu_nhap_truoc_thue_0ds;
                        $d->tong_non_tax = $d->tong_non_tax_0ds;
                        $d->tong_tnct = $d->tong_tnct_0ds;
                        $d->bhxh = $d->bhxh_0ds;
                        $d->thue_tam_trich = $d->thue_tam_trich_0ds;
                        $d->thuc_nhan = $d->thuc_nhan_0ds;
                        $d->giam_tru_ban_than = $d->giam_tru_ban_than_0ds;
                        $d->giam_tru_gia_canh = $d->giam_tru_gia_canh_0ds;
                    }
                }

            }
        }

        if (isset($data) && $data) {
            $report[0]['tong_thu_nhap_truoc_thue'] += $data->sum('tong_thu_nhap_truoc_thue');
            $report[0]['tong_non_tax'] += $data->sum('tong_non_tax');
            $report[0]['tong_tnct'] += $data->sum('tong_tnct');
            $report[0]['bhxh'] += $data->sum('bhxh');
            $report[0]['thue_tam_trich'] += $data->sum('thue_tam_trich');
            $report[0]['thuc_nhan'] += $data->sum('thuc_nhan');
            $report[0]['giam_tru_ban_than'] += $data->sum('giam_tru_ban_than');
            $report[0]['giam_tru_gia_canh'] += $data->sum('giam_tru_gia_canh');
        }

        if ($dataIsAdd) {
            foreach ($dataIsAdd as &$d) {
                if ($d->is_salary > 0) {
                    $d->tong_thu_nhap_truoc_thue = $d->sum_thu_nhap_truoc_thue;
                    $d->tong_non_tax = $d->sum_non_tax;
                    $d->tong_tnct = $d->sum_tnct;
                    $d->bhxh = $d->sum_bhxh;
                    $d->thue_tam_trich = $d->sum_thue_tam_trich;
                    $d->thuc_nhan = $d->sum_thuc_nhan;
                    $d->giam_tru_ban_than = $d->sum_giam_tru_ban_than;
                    $d->giam_tru_gia_canh = $d->sum_giam_tru_gia_canh;
                } else {
                    if ($d->is_status == 0) {
                        $d->tong_thu_nhap_truoc_thue = $d->tong_thu_nhap_truoc_thue_0ds;
                        $d->tong_non_tax = $d->tong_non_tax_0ds;
                        $d->tong_tnct = $d->tong_tnct_0ds;
                        $d->bhxh = $d->bhxh_0ds;
                        $d->thue_tam_trich = $d->thue_tam_trich_0ds;
                        $d->thuc_nhan = $d->thuc_nhan_0ds;
                        $d->giam_tru_ban_than = $d->giam_tru_ban_than_0ds;
                        $d->giam_tru_gia_canh = $d->giam_tru_gia_canh_0ds;
                    }
                }

            }
        }

        if (isset($dataIsAdd) && $dataIsAdd) {
            $reportIsAdd[0]['tong_thu_nhap_truoc_thue'] += $dataIsAdd->sum('tong_thu_nhap_truoc_thue');
            $reportIsAdd[0]['tong_non_tax'] += $dataIsAdd->sum('tong_non_tax');
            $reportIsAdd[0]['tong_tnct'] += $dataIsAdd->sum('tong_tnct');
            $reportIsAdd[0]['bhxh'] += $dataIsAdd->sum('bhxh');
            $reportIsAdd[0]['thue_tam_trich'] += $dataIsAdd->sum('thue_tam_trich');
            $reportIsAdd[0]['thuc_nhan'] += $dataIsAdd->sum('thuc_nhan');
            $reportIsAdd[0]['giam_tru_ban_than'] += $dataIsAdd->sum('giam_tru_ban_than');
            $reportIsAdd[0]['giam_tru_gia_canh'] += $dataIsAdd->sum('giam_tru_gia_canh');
        }
//        usort($data->items, function($v1, $v2) {
//
//        });
//        d($data);
        if (isset($getData['isHtml']) && $getData['isHtml']) {
            $r = view('includes.component.table_tncn', compact('data', 'header', 'report', 'dataNotStatus', 'dataIsAdd', 'reportIsAdd'))->render();

            return response()->json($r);
        }
        return response()->json($data);
    }

    public function getList(Request $request)
    {
        Topica::canOrAbort('index.tn');
        $getData = $request->all();
        $getData['select'] = "employee_code,employee_table,phap_nhan,san_pham,ma_so_thue, SUM(tong_thu_nhap_truoc_thue) as tong_thu_nhap_truoc_thue, SUM(tong_non_tax) as tong_non_tax, SUM(tong_tnct) as tong_tnct, SUM(bhxh) as bhxh, SUM(thue_tam_trich) as thue_tam_trich, SUM(thuc_nhan) as thuc_nhan, SUM(giam_tru_ban_than) as giam_tru_ban_than, SUM(giam_tru_gia_canh) as giam_tru_gia_canh, CONCAT(MONTH,'/', YEAR) as month_year,sum(case when type = 1 then 1 else 0 end) as is_salary,sum(case when status = 1 then 1 else 0 end) as is_status";
        $getData['group_by'] = ['employee_code', 'month', 'year', 'phap_nhan'];
        $getData['order_by_raw'] = "CONCAT(MONTH,'/', YEAR) DESC";
//        $getData['sort_by'] = "DESC";
        $data = $this->summaryRepository->getDataBy($getData);

//        dd($getData);
//        dd($data);
        if (isset($getData['isHtml']) && $getData['isHtml']) {
            $header = getListTableSummary();
            unset($getData['group_by']);
            $getData['select'] = " 
                                SUM(tong_tnct) as tong_tnct,
                                SUM(tong_thu_nhap_truoc_thue) as tong_thu_nhap_truoc_thue,
                                SUM(tong_non_tax) as tong_non_tax,
                                SUM(tong_tnct) as tong_tnct,  
                                SUM(bhxh) as bhxh,
                                SUM(thue_tam_trich) as thue_tam_trich,
                                SUM(thuc_nhan) as thuc_nhan
                            ";
            $report = $this->summaryRepository->getDataBy($getData, false);
            $r = view('includes.component.table', compact('data', 'header', 'report'))->render();
            return response()->json($r);
        }
        return response()->json($data);

    }

    public function getEmployeeInfo(Request $request)
    {
        $data = $request->all();
        try {
            if (!isset($data['phap_nhan'])) {
                throw new \Exception('Không có pháp nhân');
            }
            if (!isset($data['employee_code'])) {
                throw new \Exception('Không có employee_code');
            }
            if (!isset($data['month_year'])) {
                throw new \Exception('Không có month_year');
            }
            $salary = $this->summaryRepository->getDataBy([
                'phap_nhan' => $data['phap_nhan'],
                'employee_code' => $data['employee_code'],
                'month' => explode('/', $data['month_year'])[0],
                'year' => explode('/', $data['month_year'])[1],
                'type' => SALARY,
                'status' => "all"
            ], false)->first();
            if ($salary) {
                if ($salary->data) {

                    $report = view('includes.viewEmployee', ['employee' => json_decode($salary->data, true)])->render();
                } else {
                    $report = "Thu nhập chi tiết trước tháng 8/2018, Thầy/ Cô vui lòng xem chi tiết tại mục <i class=\"fa fa-table\" style=\"color: blue\" aria-hidden=\"true\"></i>";
                }
            } else {
                $report = "Thu nhập chi tiết trước tháng 8/2018, Thầy/ Cô vui lòng xem chi tiết tại mục <i class=\"fa fa-table\" style=\"color: blue\" aria-hidden=\"true\"></i>";
            }
            $return = [
                'status' => 1,
                'message' => $report,
                'data' => $data
            ];
        } catch (\Exception $exception) {
            $return = [
                'status' => 0,
                'message' => $exception->getMessage(),
                'data' => $data
            ];
        }

        return response()->json($return);
    }

    public function getEmployeeInfoFtt(Request $request)
    {
        $data = $request->all();
        try {
            if (!isset($data['phap_nhan'])) {
                throw new \Exception('Không có pháp nhân');
            }
            if (!isset($data['employee_code'])) {
                throw new \Exception('Không có employee_code');
            }
            if (!isset($data['month_year'])) {
                throw new \Exception('Không có month_year');
            }
            $salary = $this->summaryRepository->getAllFttOfEmployee([
                'phap_nhan' => $data['phap_nhan'],
                'employee_code' => $data['employee_code'],
                'month' => explode('/', $data['month_year'])[0],
                'year' => explode('/', $data['month_year'])[1],
                'is_additional_order' => isset($data['is_additional_order']) ? $data['is_additional_order'] : ''
            ], false);
            if ($salary) {
                $report = view('includes.viewEmployeeFtt', ['data' => $salary])->render();
            } else {
                $report = "Không có dữ liệu";
            }
            $return = [
                'status' => 1,
                'message' => $report,
                'data' => $data
            ];
        } catch (\Exception $exception) {
            $return = [
                'status' => 0,
                'message' => $exception->getMessage(),
                'data' => $data
            ];
        }
        return response()->json($return);
    }


    /**
     * Data cho danh sach thu nhap
     * @return array
     */
    public function detailDSTN($condition = [])
    {
        $report = $this->summaryRepository->getDataBy($condition, false);

        return $report;
    }

    /**
     *
     * Export excel
     */
    public function exportDSTN(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        // $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        // $cacheSettings = array( ' memoryCacheSize ' => '8MB');
        // \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $getDataBy = $request->all();
        $month = '';
        $year = '';
        $phap_nhan = '';
        $months = [];
        $search = '';

        if (isset($getDataBy['month']) && $getDataBy['month']) {
            $month = $getDataBy['month'];
        } else {
            $month = '';
        }

        if (isset($getDataBy['phap_nhan']) && $getDataBy['phap_nhan']) {
            $phap_nhan = $getDataBy['phap_nhan'];
        }

        if (isset($getDataBy['year']) && $getDataBy['year']) {
            $year = $getDataBy['year'];
        } else {
            $year = '';
        }
        if (isset($getDataBy['search']) && $getDataBy['search']) {
            $search = $getDataBy['search'];
        } else {
            $search = '';
        }
        Topica::canOrRedirect('index.tn');

        // Tao thu muc temp
        if (!is_dir(base_path() . '/storage/temp')) {
            mkdir(base_path() . '/storage/temp');
        }

        $familyName = Auth::user()->id;
        $fileName = $familyName . 'Export.zip';
        $zipFIle = base_path() . '/storage/temp/dstn' . $fileName;

        $zip = new \ZipArchive();
        $zip->open($zipFIle, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $listExportFile = [];

        // Lap qua tung thang tung phap nhan de tao file excel tuong ung
        $data = $this->summaryRepository->summaryOrderEmpData($phap_nhan, $month, $year);
        $index = 0;
        if ($data->count()) {
            $data->chunk(5000, function ($sums) use (&$index, $getDataBy, $phap_nhan, $month, $year, $familyName, $zip, &$listExportFile) {
                $index += 1;
                $eFile = base_path() . '/storage/temp/' . $phap_nhan . '-thang-' . $month . '-nam-' . $year . '-' . $index . '-' . $familyName . "-danh-sach-thu-nhap.xls";
                $originName = $phap_nhan . '-thang-' . $month . '-nam-' . $year . '-' . $index . '-' . $familyName . "-danh-sach-thu-nhap.xls";
                $listExportFile[] = $eFile;
                $this->renderExcelDSTN($sums, $month, $year, $phap_nhan, $zip, $eFile, $originName);
            });
        } else {
            exit();
        }
        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename = $fileName");
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($zipFIle);

        // Delete zip file
        foreach ($listExportFile as $fi) {
            unlink($fi);
        }
        // Delete zip file
        unlink($zipFIle);
        exit();
    }

    public function renderExcelDSTN($report, $month = '', $year = '', $phap_nhan = '', $zip = null, $filename = '', $originName = '')
    {
        //Export excel
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
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
            'font' => array(
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
            'font' => array(
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
            'font' => array(
                'bold' => true
            ), 'borders' => array(
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

        $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "DANH SÁCH THU NHẬP ")->getStyle('A1')->applyFromArray($style)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Tháng")->getStyle('A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', $month . '/' . $year);

        // Giá trị năm
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Pháp nhân")->getStyle('A3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', $phap_nhan);

        //header
        $objPHPExcel->getActiveSheet()->getStyle('A5:R5')->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->SetCellValue('A5', "STT");
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', "Tháng");
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', "Tên");
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', "Mã NV");
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', "Pháp Nhân");
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', "Serial");

        $objPHPExcel->getActiveSheet()->SetCellValue('G5', "Tổng thu nhập trước thuế");
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', "Tổng Non Tax");
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', "Tổng TNCT");

        $objPHPExcel->getActiveSheet()->SetCellValue('J5', "BHXH");
        $objPHPExcel->getActiveSheet()->SetCellValue('K5', "Thuế tạm trích");
        $objPHPExcel->getActiveSheet()->SetCellValue('L5', "Thực nhận");

        $objPHPExcel->getActiveSheet()->SetCellValue('M5', "Giảm trừ bản thân");
        $objPHPExcel->getActiveSheet()->SetCellValue('N5', "Giảm trừ gia cảnh");
        $objPHPExcel->getActiveSheet()->SetCellValue('O5', "Bộ chứng từ");

        $objPHPExcel->getActiveSheet()->SetCellValue('P5', "Loại chứng từ");
        $objPHPExcel->getActiveSheet()->SetCellValue('Q5', "Ngày thanh toán");
        $objPHPExcel->getActiveSheet()->SetCellValue('R5', "Diễn giải");

        $startRender = 6;
        $lastRow = 6;
        $fullnames = [];


        foreach ($report as $key => $row) {
            // Get ful name
            $renderRow = $key + $startRender;
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $renderRow, $key + 1);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $renderRow, $row->month . '/' . $row->year);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $renderRow, $row->full_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $renderRow, $row->employee_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $renderRow, $row->phap_nhan);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $renderRow, $row->serial);

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $renderRow, $row->tong_thu_nhap_truoc_thue);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $renderRow, $row->tong_non_tax);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $renderRow, $row->tong_tnct);

            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $renderRow, $row->bhxh);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $renderRow, $row->thue_tam_trich);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $renderRow, $row->thuc_nhan);

            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $renderRow, $row->giam_tru_ban_than);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $renderRow, $row->giam_tru_gia_canh);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $renderRow, $row->order_id);

            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $renderRow, $row->type_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $renderRow, $row->ngay_thanh_toan);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $renderRow, $row->noi_dung);

            $lastRow = $renderRow;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A' . $startRender . ':' . 'R' . $lastRow)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $startRender . ':' . 'L' . $lastRow)->getNumberFormat()->setFormatCode('###,##0');
        $objPHPExcel->getActiveSheet()->getStyle('G' . ($lastRow + 1) . ':' . 'L' . ($lastRow + 1))->getNumberFormat()->setFormatCode('###,##0');

        $objPHPExcel->getActiveSheet()->SetCellValue('F' . ($lastRow + 1), "Tổng")->getStyle('F' . ($lastRow + 1))->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->SetCellValue('G' . ($lastRow + 1), "=SUM(G{$startRender}:G{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . ($lastRow + 1), "=SUM(H{$startRender}:H{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . ($lastRow + 1), "=SUM(I{$startRender}:I{$lastRow})");

        $objPHPExcel->getActiveSheet()->SetCellValue('J' . ($lastRow + 1), "=SUM(J{$startRender}:J{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('K' . ($lastRow + 1), "=SUM(K{$startRender}:K{$lastRow})");
        $objPHPExcel->getActiveSheet()->SetCellValue('L' . ($lastRow + 1), "=SUM(L{$startRender}:L{$lastRow})");

        foreach (range('A', 'R') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->SetCellValue('C' . ($lastRow + 5), "Kế toán tổng hợp")->getStyle('C' . ($lastRow + 5))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('N' . ($lastRow + 5), "TCB")->getStyle('N' . ($lastRow + 5))->getFont()->setBold(true);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        $zip->addFile($filename, $originName);

    }

}

