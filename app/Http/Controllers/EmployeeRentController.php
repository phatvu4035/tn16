<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Requests\CreateEmpRentRequest;
use App\Http\Requests\EditEmpRentRequest;
use Illuminate\Http\Request;

class EmployeeRentController extends Controller
{
    //
    protected $empRentRepository;

    public function __construct(EmpRentRepositoryInterface $empRentRepository)
    {
        $this->empRentRepository = $empRentRepository;
    }

    public function index(Request $request)
    {
        Topica::canOrRedirect('index.rent_employee');
        $getData = $request->all();
        $data = $this->empRentRepository->getDataBy($getData);
        return view('emp_rent.index', compact('data', 'getData'));
    }

    public function create()
    {
        Topica::canOrRedirect('add.rent_employee');
        return view('emp_rent.create');
    }

    public function store(CreateEmpRentRequest $request)
    {
        Topica::canOrRedirect('add.rent_employee');
        $data = $request->all();
        $empRent = $this->empRentRepository->saveData($data);
        if ($empRent) {
            $request->session()->flash('success', 'Lưu thành công!');
            return redirect()->route('emp_rent.index')->with(['response' => 1]);
        } else {
            $request->session()->flash('errors', 'Lưu thất bại!');
            return redirect()->route('emp_rent.index')->with(['response' => 0]);
        }

    }

    public function edit($id)
    {
        Topica::canOrRedirect('edit.rent_employee');
        $empRent = $this->empRentRepository->getDataBy(['id' => $id, 'working_status' => 'all'])->first();
        return view('emp_rent.create', compact('empRent'));
    }


    public function update(EditEmpRentRequest $request)
    {
        Topica::canOrRedirect('edit.rent_employee');
        $data = $request->all();
        $data['trashed'] = true;
        $empRent = $this->empRentRepository->saveData($data);
        if ($empRent) {
            $request->session()->flash('success', 'Lưu thành công!');
            return redirect()->route('emp_rent.index')->with(['response' => 1]);
        } else {
            $request->session()->flash('errors', 'Lưu thất bại!');
            return redirect()->route('emp_rent.index')->with(['response' => 0]);
        }
    }

    public function destroy($id)
    {
        Topica::canOrRedirect('delete.rent_employee');
        $this->empRentRepository->destroy($id);
        return redirect()->route('emp_rent.index');
    }

    public function restoreEmpRent($id)
    {
        Topica::canOrRedirect('delete.rent_employee');
        $empRent = $this->empRentRepository->getAllData()->find($id);
        $empRent->restore();
        return redirect()->route('emp_rent.index')->with('success', 'Nhân sự '.$empRent->emp_name.' chuyển về trạng thái làm việc.');
    }
}
