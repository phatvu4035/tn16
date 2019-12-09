@extends('layouts.app')

@section('content')
    <style>
        #small-phap_nhan, #small-year {
            margin-top: 6px;
        }
    </style>
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <h3 class="c-grey-900">Báo cáo 401</h3>
        <div class="mT-30">
            {{Form::openForm('',['method'=>'POST','route'=>'export.401.post','id'=>'form-401'])}}
            <div class="form-row">
                <div class="form-group col-md-4">
                    {{
                        Form::dropDown([
                            'label' => 'Pháp nhân <span class="c-r">*</span>',
                            'name' => 'phap_nhan',
                            'data' => [],
                            'noDefault' => true,
                            'options' => [
                                'id' => 'phap_nhan',
                                'class' => 'phap_nhan',
                                'required' => true
                            ],
                        ])
                    }}
                </div>
                <div class="form-group col-md-4">
                    @php
                        $month = [
                        '01'=>'Tháng 1',
                        '02'=>'Tháng 2',
                        '03'=>'Tháng 3',
                        '04'=>'Tháng 4',
                        '05'=>'Tháng 5',
                        '06'=>'Tháng 6',
                        '07'=>'Tháng 7',
                        '08'=>'Tháng 8',
                        '09'=>'Tháng 9',
                        '10'=>'Tháng 10',
                        '11'=>'Tháng 11',
                        '12'=>'Tháng 12',
                    ]
                    @endphp
                    {{ Form::dropDown([
                            'label' => 'Tháng <span class="c-r">*</span>',
                            'name' => 'month',
                            'data' => array_flip($month),
                            'selected'=>isset($data['month'])?$data['month']:"",
                            'options' => [
                                'id' => 'month',
                            ],
                        ])}}
                </div>
                <div class="form-group col-md-4">
                    @php
                        $year = [];
                        for($y = 2018;$y<=date('Y');$y++){
                            $year[$y]=$y;
                        }
                    @endphp
                    {{ Form::dropDown([
                            'label' => 'Năm <span class="c-r">*</span>',
                            'name' => 'year',
                            'data' => $year,
                            'selected'=>isset($data['year'])?$data['year']:(old('year')?old('year'):date('Y')),
                            //'selected'=>old('year'),
                            'options' => [
                                'id' => 'year',
                            ],
                        ])}}
                </div>
                <div class="form-group col-md-4">
                    {{ Form::dropDown([
                            'label' => 'Xuất dữ liệu <span class="c-r">*</span>',
                            'name' => 'type_data',
                            'data' => [
                            'Theo tháng'=>"1",
                            'Theo ngày thanh toán(dành cho dữ liệu cũ)'=>"2"
                            ],
                            'selected'=>'1',
                            //'selected'=>old('year'),
                            'options' => [
                                'id' => 'year',
                            ],
                        ])}}
                </div>

            </div>
            <div class="form-row" style="padding-top: 10px">
                <div class="form-group col-md-3">
                    <button type="submit" value="download"
                            class="btn btn-primary" id="download401"><i class="fa fa-download"></i> Download
                    </button>
                </div>
            </div>
            <div class="form-row">
                {{--@include('export.components.table401')--}}
            </div>
            {{Form::closeForm()}}
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            // alert(;)
            $('#download401').click(function (e) {
                $('#small-phap_nhan').html('');
                $('#small-month').html('');
                $('#small-year').html('');
            });
        });

        $("#phap_nhan").select2({
            language: "vi",
            placeholder: 'Chọn pháp nhân',
            allowClear: true,
            ajax: {
                url: '/dm4c/pt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });
    </script>
@endsection