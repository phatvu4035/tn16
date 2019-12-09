<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 11:23 AM
 */

namespace App\Http\Controllers;


use App\Http\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class UserController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $data = $this->userRepository->getAll();


//        Excel::create('newfile', function($excel) {
//
//            $excel->sheet('Sheetname', function($sheet) {
//
//                $sheet->fromArray(array(
//                    array('data1', 'data2'),
//                    array('data3', 'data4')
//                ));
//
//            });
//
//        })->store('xls', storage_path('excel/exports'));
        $d = [];
        $a = Excel::load(storage_path('excel/exports').'/newfile.xls', function($reader) use(&$d) {

        })->get()->toArray();
        dd($a);
        foreach ($a as $b){
            dd($b->getItem());
        }
        // Trả dữ liệu data ra view
        return view('users.index',compact('data'));
    }
}