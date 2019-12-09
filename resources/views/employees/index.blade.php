@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h4 class="c-grey-900 mT-10 mB-30">Danh sách nhân sự</h4>


        {{Form::openForm('Import HR20',['method'=>'POST','route'=>'employees.store','id'=>'form-import','files' => true])}}
        <div class="form-row">
            <div class="form-group">
                <label class="fw-500" style="padding-right: 20px">Chọn file hr20 (.xlsx, xls)</label>
                <input type="file" id="importFile" name="importFile">
                <button type="submit" class="btn btn-primary" id="btnUpload">Import
                </button>
            </div>
        </div>
        {{Form::closeForm()}}
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">MNV</th>
                        <th scope="col">Tên</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employee as $e)
                    <tr>
                        <th scope="row">1</th>
                        <td>{{$e->employee_code}}</td>
                        <td>{{$e->last_name}} {{$e->first_name}}</td>
                        <td>{{$e->email}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection