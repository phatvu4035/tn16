@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @include('includes.list_emp_rent',[
                'title'=>'Nhân sự thuê khoán',
                'filter'=>[
                    [
                        'type'=>"text",
                        'name'=>'search',
                        'placeholder'=>"Nhập thông tin tên hoặc số thẻ"
                    ],
                    [
                        'type'=>"select",
                        'name'=>'working_status',
                        'option' => [
                            'all' => 'Toàn bộ nhân sự',
                            'working' => 'Đang làm việc',
                            'non_working' => 'Dừng làm việc',
                        ],
                    ]
                ],
                 'adding' => 'emp_rent.create',
                 'checkRole'=>[
                    'add'=>'add.rent_employee',
                    'edit'=>'edit.rent_employee',
                    'delete'=>'delete.rent_employee'
                 ]
            ])
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            listTable.init({
                url: '{{route('emp_rent.api.list')}}'
            });
        });

    </script>
@endsection