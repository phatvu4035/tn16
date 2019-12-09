@extends('layouts.app')

@section('custom-css')
    <link rel="stylesheet" type="text/css" href=" {{ asset('css/topican.css') }} ">
@endsection

@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @include('includes.list',[
                'title'=>'Danh sách tài khoản',
                'filter'=>[
                    [
                        'type'=>"text",
                        'name'=>'search',
                        'placeholder'=>"Nhập tên hoặc email"
                    ],
                ],
                'adding' => 'topican.create',
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
                url: '{{ route('topican.api.list')}}'
            });
        });

        $('.form-delete').submit(function (e) {
            let result = confirm("Bạn có chắc muốn khoá tài khoản ?");

            if (!result) {
                e.preventDefault();
            }
        });

    </script>
    <script>

        @if ($errors->any())
        @foreach ($errors->all() as $error)
        makeAlert('Lỗi:', "{{ $error }}", 'warning');
        @endforeach
        @endif

        @if(session()->has('error'))
        makeAlert('Lỗi:', "{{ session()->get('error') }}", 'warning');
        @endif

        {{--@if(session()->has('message'))--}}
        {{--makeAlert('Thành công:', "{{ session()->get('message') }}", 'success');--}}
        {{--@endif--}}

    </script>

@endsection

