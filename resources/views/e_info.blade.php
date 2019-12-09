@extends('layouts.app')

@section('content')
    <style>
        .w-100px {
            width: 100px;
        }

        #phap_nhan + .select2-container {
            width: 217px !important
        }

        #collapseExample1 .row .col:first-child {
            font-weight: bold;
        }

        #collapseExample1 .row .col:nth-child(2) {
            margin-left: 10px;
            margin-bottom: 10px;
        }
    </style>
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @php
                $year = [];
                for($y = 2018;$y<=date('Y');$y++){
                    $year[$y]=$y;
                }
            @endphp
            @include('includes.list_e_info',[
                'title'=>'Tra cứu chi tiết thu nhập của cá nhân',
                'not_btn_search'=>false,
                'filter'=>[
                    [
                        'type'=>"text",
                        'name'=>'employee_code',
                        'placeholder'=>"Mã nhân viên",
                        'id' => "employee_code"
                    ],
                    [
                        'type'=>"select",
                        'name'=>'phap_nhan',
                        'placeholder'=>"Pháp nhân",
                        'id' => "phap_nhan",
                        'option'=>[]
                    ],
                    [
                        'type'=>"select",
                        'name'=>'month',
                        'placeholder'=>"Tháng",
                        'value'=>"",
                        'option'=>[
                            ''=>'Chọn tháng',
                            '1'=>'Tháng 1',
                            '2'=>'Tháng 2',
                            '3'=>'Tháng 3',
                            '4'=>'Tháng 4',
                            '5'=>'Tháng 5',
                            '6'=>'Tháng 6',
                            '7'=>'Tháng 7',
                            '8'=>'Tháng 8',
                            '9'=>'Tháng 9',
                            '10'=>'Tháng 10',
                            '11'=>'Tháng 11',
                            '12'=>'Tháng 12',
                        ]
                    ],
                    [
                        'type'=>"select",
                        'name'=>'year',
                        'placeholder'=>"Năm",
                        'value'=>date('Y'),
                        'option'=>$year
                    ],
                ]
            ])
            <div class="mT-30">
                <div class="row">
                    <div class="col col-md-6">
                        <p>
                            <a class="btn btn-secondary" data-toggle="collapse" href="#collapseExample" role="button"
                               aria-expanded="true" aria-controls="collapseExample">
                                <i class="fa fa-exclamation-triangle"></i> Lưu ý
                            </a>
                        </p>
                        <div class="collapse show" id="collapseExample">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col col-md-1"><i class="fa fa-flag" style="color: #0f9aee;"
                                                                 aria-hidden="true"></i></div>
                                    <div class="col col-md-11">Đã qua kiểm tra, đã được ghi nhận bởi TCB và kế toán
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-1"><i class="fa fa-flag" style="color: red "
                                                                 aria-hidden="true"></i></div>
                                    <div class="col col-md-11">Đã được ghi nhận bởi TCB, đang trong quá trình kiểm tra
                                        giữa TCB và kế toán
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-1"><i class="fa fa-table" style="color: blue"
                                                                 aria-hidden="true"></i></div>
                                    <div class="col col-md-11">Hiển thị nhanh com, thưởng</div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-1"><i class="fa fa-eye" style="color: #0f9aee;"
                                                                 aria-hidden="true"></i></div>
                                    <div class="col col-md-11">Hiển thị chi tiết bảng lương</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-md-6">
                        <p class="text-right">
                            <button class="btn btn-secondary" type="button" data-toggle="collapse"
                                    data-target="#collapseExample1" aria-expanded="true"
                                    aria-controls="collapseExample">
                                <i class="fa fa-columns"></i> Giải thích các cột
                            </button>
                        </p>
                        <div class="collapse show" id="collapseExample1">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col col-md-12">Tổng thu nhập trước thuế</div>
                                    <div class="col col-md-12">Tổng thu nhập của nhân viên từ các khoản (lương, com,
                                        thưởng, khác...) trước khi bị đánh thuế Thu nhập cá nhân
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Tổng TN không chịu thuế</div>
                                    <div class="col col-md-12">Tổng các khoản thu nhập của nhân viên không phải chịu
                                        thuế Thu nhập cá nhân
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Tổng TNCT</div>
                                    <div class="col col-md-12">Tổng các khoản thu nhập của nhân viên phải chịu thuế thu
                                        nhập cá nhân
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">BHXH</div>
                                    <div class="col col-md-12">Bảo hiểm xã hội nhân viên bắt buộc phải đóng</div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Pháp nhân</div>
                                    <div class="col col-md-12">Công ty mà nhân viên ký hợp đồng lao động cùng tại thời
                                        điểm phát sinh thu nhập
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Giảm trừ gia cảnh</div>
                                    <div class="col col-md-12">Số tiền được giảm trừ cho người phụ thuộc của nhân viên
                                        (3.600.000 đồng/người phụ thuộc)
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Giảm trừ bản thân</div>
                                    <div class="col col-md-12">Giảm trừ cho bản thân nhân viên khi tính thuế thu nhập cá
                                        nhân (9.000.000 đồng)
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Thuế</div>
                                    <div class="col col-md-12">Số thuế Thu nhập cá nhân tạm trích theo quy định trong
                                        tháng
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12">Thực nhận</div>
                                    <div class="col col-md-12">Tổng số tiền nhân viên nhận được sau khi thực hiện đầy đủ
                                        các nghĩa vụ cá nhân trong tháng
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            listTable.init({
                url: '{{route('summary.api.tn18')}}'
            });
        });

        $("#phap_nhan").select2({
            language: "vi",
            placeholder: 'Chọn pháp nhân',
            allowClear: true,
            ajax: {
                url: '/dm4c/pt/me',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

    </script>
@endsection