<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Helpers\ImportExcel;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    protected $importExcel;

    public function __construct(ImportExcel $importExcel)
    {


        $this->importExcel = $importExcel;
    }

    public function index()
    {
        $employee = Employee::all();
        return view('employees.index', compact('employee'));
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        ini_set('upload_max_filesize', '-1');
        ini_set('post_max_size', '-1');

        config(['excel.import.startRow' => 1]);
        config(['excel.import.heading' => true]);
        $dataExcel = $this->importExcel->getData($request->file('importFile'))->toArray();
//        dd($dataExcel);
        foreach ($dataExcel as $excel) {
//            if ($excel[11] != "HRB9000-Đã nghỉ việc") {
                $employee = Employee::firstOrNew(['employee_code'=>$excel['ma_nhan_vien']]);
//                dd($employee);
                $employee->employee_code = $excel['ma_nhan_vien'];

                $employee->first_name = $excel['ten'];
                $employee->last_name = $excel['ho_va_ten_dem'];

                $employee->email = $excel['email_cong_ty'];

                $employee->cmt = $excel['so_cmnd'];

                $employee->phap_nhan = isset($excel['phap_nhan']) ? (trim(explode('-', $excel['phap_nhan'])[0])) : '';

                $employee->mst = $excel['ma_so_thue'];

                $employee->vi_tri = isset($excel['ngach']) ? (trim(explode('-', $excel['ngach'])[0])) : '';

                $employee->save();
//            }

        }
//        dd($dataExcel->toArray());
    }

    public function slipName($name)
    {
        $name = explode(' ', $name);
        $first_name = $name[count($name) - 1];
        unset($name[count($name) - 1]);
        $last_name = implode(' ', $name);
        return [
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
    }

    public function viewEmployee(){
        Topica::canOrRedirect('Adminstrator');
        return view('e_info');
    }

}
