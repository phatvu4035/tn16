@extends('layouts.app')

@section('content')
    <style>
        .tabulator .tabulator-header .tabulator-col .tabulator-col-content .tabulator-col-title {
            overflow: unset !important;
        }

        .tabulator-col[role=columnheader] {
            /*height: 100px;*/
        }

        .tabulator-col[role=columnheader] .tabulator-col-title {
            white-space: normal !important;
        }

        #small-phap_nhan, #small-year {
            margin-top: -12px;

        }

        .alert-warning {
            background-color: #fffbd8 !important;
        }

        .other-color {
            background-color: #0088cc !important;
            color: #bce8f1;
        }

    </style>
    <div class="row gap-20 masonry pos-r">

        <div class="masonry-sizer col-md-12"></div>
        <div class="masonry-item col-md-12">
            {{Form::openForm('Import Summary',['method'=>'POST','route'=>'import.old.value.post','id'=>'form-import','files' => true])}}
            {{--<input type="hidden" name="order" value="{{json_encode($request['order'])}}">--}}
            {{--<input type="hidden" name="month" value="{{isset($request['month'])?$request['month']:0}}">--}}
            {{--<input type="hidden" name="year" value="{{isset($request['year'])?$request['year']:0}}">--}}

            {{--            <input type="hidden" name="dataExcel" value="{{json_encode($dataImport)}}">--}}

            <div class="form-row">
                <div class="form-group col-md-4">
                    {{ Form::dropDown([
                                    'label' => "Pháp nhân *",
                                    'name' => 'phap_nhan',
                                    'data'=>[],
                                    'selected'=>isset($request['phap_nhan'])?$request['phap_nhan']:"",
                                    'options' => [
                                        'placeholder' => '',
                                        'class' => 'form-group',
                                        ],
                                ]) }}
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
                            'label' => 'Tháng *',
                            'name' => 'month',
                            'data' => array_flip($month),
                            'selected'=>isset($request['month'])?$request['month']:0,
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
                           'selected'=>isset($request['year'])?$request['year']:(old('year')?old('year'):date('Y')),
                            //'selected'=>old('year'),
                            'options' => [
                                'id' => 'year',
                            ],
                        ])}}
                </div>
            </div>
            <div class="form-group">
                <div class="alert alert-primary" style="padding-bottom: 10px">
                    <ul class="list-unstyled">
                        <li style="padding-bottom: 10px">
                            <p>Checklist đảm bảo file đúng dữ liệu</p>
                            <ul>
                                <li>Không có merge cell trong file excel</li>
                                <li>Các cột Mã NV, ID, Mã số thuế phải được format cell thành dạng text</li>
                                <li>Cột Note phải là các dữ liệu trong danh sách loại chứng từ: <a
                                            href="{{route('type.index')}}">Xem tại đây</a></li>
                                <li>Đảm bảo cột STT có dữ liệu để dễ xác định dòng dữ liệu bị lỗi</li>
                                <li>Trường hợp dữ liệu không tìm thấy trên HR20, có thể là dữ liệu có Mã NV bị sai</li>
                                <li>Trường hợp tên trong file excel khác hoàn toàn với với tên trên HR20, có thể Mã NV
                                    bị sai hoặc tên bị sai
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="fw-500" style="padding-right: 20px">Chọn file (.xlsx, xls)</label>
                    <input type="file" id="importFile" name="importFile" onchange="checkFileEmpty()">
                    <button data-max="{{(int)(str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024)}}"
                            data-umax="{{(int)(str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024)}}"
                            type="button" class="btn btn-primary" id="btnUpload" disabled>Import
                    </button>
                </div>
            </div>
            @if(isset($dataImport) && count($dataImport))
                @if(count($dataImport)>0)
                    <input type="hidden" name="dataImport" value="{{json_encode($dataImport)}}">
                    <input type="hidden" name="dataExcel" value="{{json_encode($dataExcel)}}">
                    <div class="form-group">
                        <div class="alert alert-primary">
                            <div class="form-row">
                                <div class="col-md-3">Tổng thu nhập trước thuế</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_tn_truoc_thue'])}} đ</b>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Tổng TN không chịu thuế</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_non_tax'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Tổng TNCT</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_tnct'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Bảo hiểm xã hội</div>
                                <div class="col-md-9"><b>{{number_format($total['bhxh'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Thuế tạm trích</div>
                                <div class="col-md-9"><b>{{number_format($total['thue_tam_trich'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Thực nhận</div>
                                <div class="col-md-9"><b>{{number_format($total['thuc_nhan'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Giảm trừ bản thân</div>
                                <div class="col-md-9"><b>{{number_format($total['giam_tru_ban_than'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Giảm trừ gia cảnh</div>
                                <div class="col-md-9"><b>{{number_format($total['giam_tru_gia_canh'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Tổng số bộ F (mã Ref)</div>
                                <div class="col-md-9"><b>{{count($dataImport)}}</b></div>
                            </div>
                        </div>
                    </div>
                @endif
                @php
                    $isError = false;
                @endphp
                @if(isset($dataImport))
                    <div class="form-group">
                        <div class="alert alert-danger" style="max-height: 500px;overflow: scroll;padding-bottom: 10px">
                            <ul class="list-unstyled">
                                @foreach($dataImport as $key =>$import)
                                    @if(isset($import['error']))
                                        @php
                                            $isError = true;
                                        @endphp
                                        <li style="padding-bottom: 10px">
                                            <p>Bộ chứng từ:{{$key}} có lỗi</p>
                                            <ul>
                                                @foreach($import['error'] as $empError)
                                                    <li>{!! $empError !!}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="alert alert-warning"
                             style="max-height: 500px;overflow: scroll;padding-bottom: 10px">
                            <ul class="list-unstyled">
                                @foreach($dataImport as $key =>$import)
                                    @if(isset($import['warning']))
                                        <li style="padding-bottom: 10px">
                                            <p>Bộ chứng từ:{{$key}}</p>
                                            <ul>
                                                @foreach($import['warning'] as $empError)
                                                    <li>{!! $empError !!}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="form-group" style="display: none" id="showErrorTable">

                </div>
                @if(isset($dataImport) && $dataImport)
                    <div class="form-group">
                        <div id="salary-table" class="tabulator table-bordered"></div>
                    </div>
                    <input type="hidden" name="dataTable" value="">
                    @if(!$isError)
                        <input type="hidden" name="isSave" value="false">
                    @endif
                    <button type="button" class="btn btn-primary" id="btnSave"
                            @if($isError) disabled @else name="isSave" value="true"
                            @endif  data-action="{{route('import.old.value.post')}}">
                        Lưu
                    </button>

                    <a href="{{route('order.create.salary')}}" class="btn btn-danger" id="btnCancel">Hủy</a>
                @endif
            @else
                {{--<button type="button" class="btn btn-primary" disabled>Save</button>--}}
                {{--<button type="button" class="btn btn-danger">Cancel</button>--}}
            @endif

            {{Form::closeForm()}}

            {{--<div class="form-group" style="margin-top:-16px;margin-top:-1rem;">--}}
            {{--<label for="note">Mô tả</label>--}}
            {{--<textarea class="form-control" id="note">--}}
            {{--</textarea>--}}
            {{--</div>--}}
        </div>
    </div>


    <!-- Modal -->

    <div class="modal fade show" id="infoEmployee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" style="max-width: 80%" role="document" aria-hidden="true">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Chi tiết thu nhập</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary" style="display: none" id="btnModel" data-toggle="modal"
            data-target="#infoEmployee">Launch demo modal
    </button>
@endsection
@section('script')
    <script>
        $('#btnUpload').click(function () {
            if (validate()) {
                $('#loader').removeClass('fadeOut').addClass('fadeIn').css('opacity', 0.5);
                $('#form-import input[name=isSave]').val('false');
                $('#form-import').submit();
            }
        });

        function validate(isSave = false) {
            var check = true;
            $('#phap_nhan').removeClass('errors_style');
            $('#small-phap_nhan').html('').addClass('text-muted');
            $('#month').removeClass('errors_style');
            $('#small-month').html('').addClass('text-muted');
            $('#year').removeClass('errors_style');
            $('#small-year').html('').addClass('text-muted');

            if (!isSave) {
                if ($('#importFile').val() == '') {
                    check = false;
                }
                var maxSize = 0;
                if ($('#btnUpload').data('max') > $('#btnUpload').data('umax')) {
                    maxSize = $('#btnUpload').data('umax');
                } else {
                    maxSize = $('#btnUpload').data('max');
                }
                if ($('#importFile')[0].files[0].size > maxSize) {
                    check = false;
                    maxSizeM = maxSize / 1024 / 1024;
                    makeAlert('Lỗi', 'File quá lớn không thể import (vượt quá ' + maxSizeM + 'M)', 'danger');
                }
            }

            // check phap nhan
            var phap_nhan = $('#phap_nhan').val();
            if (!phap_nhan) {
                check = false;
                $('#phap_nhan').addClass('errors_style');
                $('#small-phap_nhan').html('Pháp nhân không được trống').removeClass('text-muted').css('color', 'red');
            }
            // check năm
            var year = $('#year').val();
            if (!year) {
                check = false;
                $('#year').addClass('errors_style');
                $('#small-year').html('Năm không được trống').removeClass('text-muted').css('color', 'red');
            }
            // check tháng
            var month = $('#month').val();
            if (!month) {
                check = false;
                $('#month').addClass('errors_style');
                $('#small-month').html('Tháng không được trống').removeClass('text-muted').css('color', 'red');
            }


            return check;

        }

        // Kiểm tra đã chọn file import hay chưa?
        function checkFileEmpty() {
            var file = $('#importFile').val();
            if (file != '') {
                var ext = file.match(/\.([^\.]+)$/)[1];
                var isExcel = false;
                switch (ext) {
                    case 'xls':
                        isExcel = true;
                        break;
                    case 'xlsx':
                        isExcel = true;
                        break;
                    case 'csv':
                        isExcel = true;
                        break;
                    default:
                        $('#importFile').val('');
                }
                if (isExcel) {
                    $('#btnUpload').attr('disabled', false);
                    @if(isset($dataImport)&&$dataImport)
                    $('#month').prop('disabled', false);
                    $('#year').prop('disabled', false);
                    $('#btnSave').prop('disabled', true);
                    @endif
                }
            }
        }

        var pointer = '<b role="presentation"></b>';
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
        @if(isset($request['phap_nhan']))

        $("#phap_nhan+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/pt/{{$request['phap_nhan']}}",
            success: function (e) {
                $("#phap_nhan").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#phap_nhan+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf

        $('#btnSave').click(function (e) {
            if (validate(true)) {
                checkPhapNhanMonthYear();
            }
        });

        function checkPhapNhanMonthYear() {
            var phap_nhan = $('#phap_nhan').val();
            var year = $('#year').val();
            var month = $('#month').val();
            $.ajax({
                url: '{{route('order.validate.phap_nhan')}}',
                data: {phap_nhan: phap_nhan, month: month, year: year},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                success: function (r) {
                    if (r.status == 1) {
                        $('#form-import input[name=isSave]').val('true');
                        $('#form-import').attr('action', $('#btnSave').data('action'));
                        $('#form-import').submit();
                    }

                    if (r.status == 0) {
                        if (confirm(r.message)) {
                            $('#form-import input[name=isSave]').val('true');
                            $('#form-import').attr('action', $('#btnSave').data('action'));
                            $('#form-import').submit();
                        }

                    }
                }
            });
        }
    </script>
@endsection