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
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use Illuminate\Http\Request;

class EmpRentController extends Controller
{
    protected $empRentRepository;

    public function __construct(EmpRentRepositoryInterface $empRentRepository)
    {
        $this->empRentRepository = $empRentRepository;
    }

    public function getList(Request $request)
    {
        Topica::canOrAbort('index.rent_employee');
        $getData = $request->all();

        $data = $this->empRentRepository->getDataBy($getData);


        if (isset($getData['isHtml']) && $getData['isHtml']) {
            $header = getListTableEmpRent();

            $r = view('includes.component.table_emp_rent', compact('data', 'header'))->render();
            return response()->json($r);
        }

        return response()->json($data);

    }
}


