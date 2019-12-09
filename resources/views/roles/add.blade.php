@extends('layouts.app')

@section('content')
    <div class="bgc-white p-20 bd">
        <div class="container-fluid">
            <h3 class="c-grey-900 mT-10 mB-30">Tạo quyền</h3>
            @if(isset($data))
                <form action="{{ route('roles.update',$data->id) }}" method="post" accept-charset="utf-8">
                    @else
                        <form action="{{ route('roles.store') }}" method="post" accept-charset="utf-8">
                            @endif

                            {{ csrf_field() }}

                            <div class="row">
                                <input type="hidden" name="id" value="{{isset($data->id)?$data->id:0}}">
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Tên quyền</label>
                                        <input type="text" class="form-control" placeholder="Tên quyền. VD: Admin"
                                               name="name"
                                               value="{{ old('name', isset($data->name)?$data->name:'') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mô tả</label>
                                        <textarea class="form-control" rows="4" placeholder="Mô tả quyền"
                                                  name="description">{{ old('description', isset($data->description)?$data->description:'') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if(in_array(Auth::user()->email,getListEmailTopica()))
                                <div class="row">
                                    <div class="col-md-12 pull-right">
                                        <a CLASS="pull-right" href="{{route('create_permission')}}">Khởi tạo permission
                                            (quyền dành cho
                                            supper)</a>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($list_permission as $key=>$perm)
                                            <tr style="background-color: #ddd">
                                                <th>{{$perm['group_name']}}</th>
                                                <td><input type="checkbox" class="check-all"
                                                           data-checkbox-all="{{$perm['group_slug']}}"> Check All
                                                </td>
                                            </tr>
                                            @foreach($perm['list'] as $list)
                                                <tr>
                                                    <td>{{$list['description']}}</td>
                                                    <td><input data-checkbox="{{$perm['group_slug']}}" type="checkbox"
                                                               @if(isset($data)  && $data->permissions->firstWhere('id',$list['id'])) checked
                                                               @endif
                                                               name="permissions[]" value="{{$list['id']}}"></td>
                                                </tr>
                                            @endforeach
                                            @if($key=='cross_check')
                                                <tr style="background-color: #ddd">
                                                    <th>Pháp nhân(Dành cho đối soát)</th>
                                                    <td><input type="checkbox" class="check-all"
                                                               data-checkbox-all="phap_nhan_doi_soat"> Check All
                                                    </td>
                                                </tr>
                                                @foreach($phap_nhan as $k=>$v)
                                                    <tr>
                                                        <td>{{$v}}</td>
                                                        <td><input data-checkbox="phap_nhan_doi_soat" type="checkbox"
                                                                   @if(isset($data)  && $data->permissionPN->firstWhere('id',$k)) checked
                                                                   @endif
                                                                   name="permissions[phap_nhan][]" value="{{$k}}"></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    @if(isset($data))
                                        <button type="submit" class="btn btn-primary" id="btnSave">
                                            Cập nhật
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary" id="btnSave">
                                            Tạo mới
                                        </button>
                                    @endif
                                    <a href="{{route('roles.index')}}" class="btn btn-danger" id="btnCancel">Quay
                                        lại</a>

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
        $(document).ready(function () {
            $('tr').click(function (event) {
                if (event.target.type !== 'checkbox') {
                    // $(':checkbox', this).prop('checked', !$(':checkbox', this).prop('checked'));
                    $(':checkbox', this).trigger('click');
                }
            });
            $('.check-all').change(function () {
                $('input[data-checkbox=' + $(this).data('checkbox-all') + ']').prop('checked', $(this).prop('checked'));
            });
            $('input[type=checkbox]').click(function () {
                if ($(this).data('checkbox')) {
                    all = $('input[type=checkbox][data-checkbox=' + $(this).data('checkbox') + ']').length;
                    check = $('input[type=checkbox][data-checkbox=' + $(this).data('checkbox') + ']:checked').length;
                    $('input[type=checkbox][data-checkbox-all=' + $(this).data('checkbox') + ']').prop('checked', all == check);
                }
            });
            $('input[type=checkbox]').each(function () {
                if ($(this).data('checkbox')) {
                    all = $('input[type=checkbox][data-checkbox=' + $(this).data('checkbox') + ']').length;
                    check = $('input[type=checkbox][data-checkbox=' + $(this).data('checkbox') + ']:checked').length;
                    if (all == check) {
                        $('input[type=checkbox][data-checkbox-all=' + $(this).data('checkbox') + ']').prop('checked', true);
                    }

                }
            });
        });
    </script>

@endsection