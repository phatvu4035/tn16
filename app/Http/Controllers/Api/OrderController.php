<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 7/5/18
 * Time: 10:10 AM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\OrderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    protected $empRentRepository;

    protected $employeeRepository;

    protected $orderRepository;

    public function __construct(EmpRentRepositoryInterface $empRentRepository, EmployeeRepositoryInterface $employeeRepository, OrderRepositoryInterface $orderRepository)
    {
        $this->empRentRepository = $empRentRepository;
        $this->employeeRepository = $employeeRepository;
        $this->orderRepository = $orderRepository;
    }

//    public function searchByCode(Request $request)
//    {
//        $getData = $request->all();
//
//        $codeType = empty($getData['code-type']) ? "" : $getData['code-type'];
//        $codeValue = empty($getData['code-value']) ? "" : $getData['code-value'];
//        $conditions = [];
//        $model = null;
//
//        switch ($codeType) {
//            case 'mnv':
//                $model = $this->employeeRepository;
//                $collumn = "employee_code";
//                $conditions = [
//                    'employee_code' => $codeValue
//                ];
//                break;
//
//            case 'hc':
//                $model = $this->empRentRepository;
//                $collumn = "identity_code";
//                $conditions = [
//                    'identity_code' => $codeValue,
//                    'identity_type' => 'hc'
//                ];
//                break;
//            case 'cmt':
//                $model = $this->empRentRepository;
//                $collumn = "identity_code";
//                $conditions = [
//                    'identity_code' => $codeValue,
//                    'identity_type' => 'cmt'
//                ];
//                break;
//
//            default:
//                $model = $this->employeeRepository;
//                $collumn = "employee_code";
//                $conditions = [
//                    'employee_code' => $codeValue
//                ];
//                break;
//        }
//
//        $data = $model->getDataBy($conditions);
//
//        return response()->json($data);
//
//    }

    public function searchByCode(Request $request)
    {
        $data = $request->all();
        try {
            if (!(isset($data['code-value']) && $data['code-value'])) {
                throw new \Exception('Bạn chưa chọn Mã NV/CMT/HC để tìm kiếm');
            }
            // lấy dữ liệu từ HR20
            $employees = $this->employeeRepository->getDataBy(['identity_code' => $data['code-value']], false);

            //kiểm tra xem có lấy nhiều hơn 1 dữ liệu
            if (count($employees) > 1) {
                $str = "Đang có " . count($employees) . " người cùng dữ liệu:";
                foreach ($employees as $e) {
                    $str .= " " . $e->employee_code . ":" . $e->last_name . " " . $e->first_name . ",";
                }
                $str .= " .Hãy tìm kiếm cách khác.";
                throw new \Exception($str);
            }
            if ($employees->isEmpty()) {
                // nếu không có HR20 thì lấy từ dữ liệu nhân sự thuê khoán
                $employees = $this->empRentRepository->getDataBy(['identity_code' => $data['code-value']], false);
            }
            //kiểm tra xem có lấy nhiều hơn 1 dữ liệu
            if (count($employees) > 1) {
                $str = "Đang có " . count($employees) . " người cùng dữ liệu:";
                foreach ($employees as $e) {
                    $str .= " " . $e->employee_code . ":" . $e->emp_name . ",";
                }
                $str .= " .Hãy tìm kiếm cách khác.";
                throw new \Exception($str);
            }
            return response()->json([
                'status' => 1,
                'messenger' => '',
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'messenger' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function checkSerial(Request $request)
    {
        $data = $request->all();
        try {
            if (!(isset($data['serial']) && $data['serial'])) {
                throw new \Exception('Serial không được để trống');
            }
            $regex = '/^(bx-[0-9]+|[0-9]+|334-T[0-9]+-[0-9]+)$/i';
            preg_match($regex, $data['serial'], $matches);
            if (empty($matches)) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Số serial phải thuộc một trong các dạng <br/> <b>bx-</b>1234567 <br/> 1234567 <br/> <b>334-T</b>4<b>-</b>123 <br> (<span style="color: red">*</span>) Phần in đậm là cố định, còn lại là dạng số',
                    'data' => $data
                ]);
            }
            if (isset($data['id']) && $data['id']) {
                $serial = $this->orderRepository->getDataBy(['serial' => $data['serial'], 'notId' => $data['id']])->first();
                $currentOrder = $this->orderRepository->getDataBy(['id' => $data['id']])->first();
                if ($currentOrder && $currentOrder->status == OrderInfo::CROSS_CHECK_DONE) {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Bạn không thể sửa serial bộ thanh toán đã đối soát',
                        'data' => $data
                    ]);
                }
            } else {
                $serial = $this->orderRepository->getDataBy(['serial' => $data['serial']])->first();
            }

            if ($serial) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Serial đã tồn tại trong hệ thống',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => '',
                    'data' => $data
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => $data
            ]);
        }

    }
}


