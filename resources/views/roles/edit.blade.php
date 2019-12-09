@extends('layouts.app')

@section('content')
    <div class="bgc-white p-20 bd">
        <div class="container-fluid">
            <h3 class="c-grey-900 mT-10 mB-30">Cập nhật quyền</h3>
            <form action="{{ route('roles.update', $data->id) }}" method="post" accept-charset="utf-8">
                {{ csrf_field() }}

                <div class="row">
                    <input type="hidden" name="id" value="{{ $data->id }}">
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Tên quyền</label>
                            <input type="text" class="form-control" placeholder="Tên quyền. VD: Admin" name="name"
                                   value="{{ old('name', $data->name ) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea class="form-control" rows="4" placeholder="Mô tả quyền"
                                      name="description">{{ old('description', $data->description ) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Permission</th>
                                <th>Mô tả</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $perm)

                                <tr>
                                    <td><input type="checkbox" name="permisions[]" value="{{ $perm->id }}"
                                        @isset($assigned[$perm->id])
                                            {{ $assigned[$perm->id] }}
                                                @endisset
                                        >
                                    </td>
                                    <td>{{ $perm->name }}</td>
                                    <td>{{ $perm->description }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" id="btnSave">
                            Sửa
                        </button>
                        <a href="{{route('roles.index')}}" class="btn btn-danger" id="btnCancel">Quay lại</a>
                        {{Form::closeForm()}}
                    </div>
                </div>

            </form>

        </div>
    </div>

@endsection

@section('script')
    <script>

        @if ($errors->any())
        @foreach ($errors->all() as $error)
        makeAlert('Lỗi:', "{{ $error }}", 'warning');
        @endforeach
        @endif

        @if(session()->has('error'))
        makeAlert('Lỗi:', "{{ session()->get('error') }}", 'warning');
        @endif

        @if(session()->has('message'))
        makeAlert('Thành công:', "{{ session()->get('message') }}", 'success');
        @endif

    </script>

@endsection