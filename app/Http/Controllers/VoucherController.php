<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use Illuminate\Http\Request;
Use App\Http\Requests\CreateOrder;
use App\Http\Repositories\Contracts\EmployeeOrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    protected $employeeOrderRepository;

    protected $orderRepository;

    protected $typeRepository;

    protected $summaryRepository;

    /**
     * VoucherController constructor.
     * @param EmployeeOrderRepositoryInterface $employeeOrderRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param TypeRepositoryInterface $typeRepository
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(EmployeeOrderRepositoryInterface $employeeOrderRepository, OrderRepositoryInterface $orderRepository, TypeRepositoryInterface $typeRepository, SummaryRepositoryInterface $summaryRepository)
    {
        $this->employeeOrderRepository = $employeeOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->typeRepository = $typeRepository;
        $this->summaryRepository = $summaryRepository;
        $this->middleware('auth');
    }

    /**
     * @param CreateOrder $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function insert(CreateOrder $order)
    {
        Topica::canOrRedirect('add.order');
        $type = $this->typeRepository->getDataBy([], false)->pluck('id', 'name')->toArray();
        unset($type['Lương NV']);
        // trường hợp tự thêm dữ liệu
        return view('vouchers/insert', [
            'request' => $order->all(),
            'type' => $type
        ]);
    }

    public function importFTT(CreateOrder $order)
    {
        $data = $order->all();
        // trường hợp import FTT
        if (isset($data['action-type']) && $data['action-type'] == 'import') {
            return view('vouchers.import', [
                'request' => $order->all()
            ]);
        }
        return redirect()->route('order.create');
    }

    public function getByOrderId($order_id)
    {
        $data = $this->summaryRepository->getDataBy([
            'order_id' => $order_id,
            'with' => ['employees', 'employeeRentWithDelete', 'typeName'],
            'status' => 'all'
        ]);
        foreach ($data as &$d) {
            if ($d['type'] == 1 && $d['data'] != null) {
                $d['value'] = $d['data'];
                $d['data'] = view('includes.viewEmployee', ['employee' => json_decode($d['data'], true)])->render();
            }
        }
//        dd($data->toArray());
        return $data;
    }

    /**
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($order_id)
    {
        Topica::canOrRedirect('edit.order');
        $order = $this->orderRepository->getDataBy(['id' => $order_id], false)->first();
        if($order->isSalary==1){
            return redirect()->route('order.orderInfo',$order_id);
        }
        $type = $this->typeRepository->getDataBy([], false)->pluck('id', 'name')->toArray();
        unset($type['Lương NV']);

        $employeeOrder = $this->summaryRepository->getDataBy(['order_id' => $order_id, 'with' => ['employees', 'employeeRentWithDelete', 'typeName'], 'status' => 'all'], false)->toArray();
//        dd($employeeOrder);
        $dataEmployeeOrder = [];
        foreach ($employeeOrder as $em) {
            $dataEmployeeOrder[] = [
                'id' => $em['id'],
                'identity_code' => isset($em['employees']['employee_code']) ? $em['employees']['employee_code'] : (isset($em['employee_rent_with_delete']['identity_code']) ? $em['employee_rent_with_delete']['identity_code'] : ''),
                'identity_type' => isset($em['employees']['employee_code']) ? 'mnv' : (isset($em['employee_rent_with_delete']['identity_type']) ? $em['employee_rent_with_delete']['identity_type'] : ''),
                'emp_pos' => isset($em['vi_tri']) ? $em['vi_tri'] : '',
                'emp_tax_code' => isset($em['employees']['employee_code']) ? '' : $em['employee_rent_with_delete']['emp_tax_code'],
                'emp_name' => isset($em['employees']['employee_code']) ? $em['employees']['last_name'] . ' ' . $em['employees']['first_name'] : (isset($em['employee_rent_with_delete']['emp_name']) ? $em['employee_rent_with_delete']['emp_name'] : ''),
                'payment_type' => $em['type_name']['name'],
                'payment_value' => $em['tong_tnct'],
                'personal_tax' => $em['thue_tam_trich'],
                'real_money' => $em['thuc_nhan'],
                'emp_code_date' => isset($em['employee_rent_with_delete']['emp_code_date']) ? $em['employee_rent_with_delete']['emp_code_date'] : '',
                'emp_code_place' => isset($em['employee_rent_with_delete']['emp_code_place']) ? $em['employee_rent_with_delete']['emp_code_place'] : '',
                'emp_country' => isset($em['employee_rent_with_delete']['emp_country']) ? $em['employee_rent_with_delete']['emp_country'] : '',
                'emp_live_status' => isset($em['employee_rent_with_delete']['emp_live_status']) ? $em['employee_rent_with_delete']['emp_live_status'] : '',
                'emp_account_number' => isset($em['employee_rent_with_delete']['emp_account_number']) ? $em['employee_rent_with_delete']['emp_account_number'] : '',
                'emp_account_bank' => isset($em['employee_rent_with_delete']['emp_account_bank']) ? $em['employee_rent_with_delete']['emp_account_bank'] : '',
                'status' => 'stable',
                'input_status' => 'search-true',
                'data_validate' => true,
                'emp_delete' => isset($em['employee_rent_with_delete']['deleted_at']) ? true : false,
            ];
        }
//        dd($employeeOrder);
        // trường hợp tự thêm dữ liệu
        return view('vouchers/insert', [
            'request' => $order,
            'typeView' => 'edit',
            'type' => $type,
            'employeeOrder' => $dataEmployeeOrder,
            'phap_nhan' => $order->phap_nhan
        ]);
    }
}
