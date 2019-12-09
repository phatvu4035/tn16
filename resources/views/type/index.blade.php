@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @include('includes.list',[
                'title'=>'Loại Chứng Từ',
                'filter'=>[
                    [
                        'type'=>"text",
                        'name'=>'search',
                        'placeholder'=>"Nhập thông tin tên hoặc số thẻ"
                    ]
                ],
                'adding' => 'type.create',
                'checkRole'=>[
                    'add'=>'add.user'
                ]
            ])
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            listTable.init({
                url: '{{route('type.api.list')}}'
            });
        });

    </script>
@endsection