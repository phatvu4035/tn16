<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 7/5/18
 * Time: 10:10 AM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployee(Request $request)
    {
        $data = $request->all();
        $result = $this->employeeRepository->getDataBy([
            'search' => isset($data['q']) && $data['q'] ? $data['q'] : " ",
            'limit' => 20
        ], false);
        return response()->json([
            'results' => $result
        ]);
    }

    public function getSingle($emp_code)
    {
        $emp = $this->employeeRepository->getDataBy([
            "employee_code" => $emp_code
        ], false)->toArray();

        if (empty($emp)) {
            $result = [
                'id' => $emp_code,
                'text' => 'Không tìm thấy'
            ];
        } else {
            $result = [
                "id" => $emp[0]['employee_code'],
                "text" => $emp[0]['last_name']." ".$emp[0]['first_name']."(".$emp[0]['employee_code'].")",
            ];
        }

        return response()->json([
            "results" => $result
        ]);
    }
}