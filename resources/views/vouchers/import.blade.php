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
    @php($isSalary = isset($request['view']) && $request['view']=='import-salary')
    @php($title=$isSalary?"Import bảng lương":"Import dữ liệu thanh toán")
    <div class="row gap-20 masonry pos-r">

        <div class="masonry-sizer col-md-12"></div>
        <div class="masonry-item col-md-12">
            @if($isSalary)
                <button class="btn btn-primary" id="btnBack" data-action="{{route('post.order.create.salary')}}">Quay
                    lại
                </button>
            @else
                <a class="btn btn-primary" href="{{route('order.create', $request)}}">Quay lại</a>
            @endif
        </div>
        <div class="masonry-item col-md-12">
            @if($isSalary)
                {{Form::openForm($title,['method'=>'POST','route'=>'order.import.file','id'=>'form-import','files' => true])}}
                <input type="hidden" name="order" value="{{json_encode($request['order'])}}">
                <input type="hidden" name="month" value="{{isset($request['month'])?$request['month']:0}}">
                <input type="hidden" name="year" value="{{isset($request['year'])?$request['year']:0}}">
            @else
                {{Form::openForm($title,['method'=>'POST','route'=>'order.import.ftt','id'=>'form-import','files' => true])}}
                <input type="hidden" name="order" value="{{json_encode($request)}}">
            @endif

            <input type="hidden" name="dataExcel" value="{{json_encode($dataExcel)}}">
            @if($isSalary)

                <div class="form-row">
                    <div class="form-group col-md-4">
                        {{ Form::inputField([
                                        'label' => "Pháp nhân *",
                                        'name' => 'phap_nhan',
                                        'value'=>isset($request['phap_nhan'])?$request['phap_nhan']:"",
                                        'options' => [
                                            'placeholder' => '',
                                            'class' => 'form-group',
                                            ],
                                    ]) }}
                    </div>
                    <div class="form-group col-md-4">
                        @php($month = [
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
                        ])
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
                        {{ Form::inputField([
                                       'label' => 'Năm *',
                                       'name' => 'year',
                                       'value'=>isset($returnData['import']['year'])?$returnData['import']['year']:date('Y'),
                                       'options' => [
                                           'placeholder' => '',
                                           'class' => 'form-group',


                                       ],
                                   ]) }}
                    </div>
                </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <p>File Mẫu: <a href="https://docs.google.com/spreadsheets/d/1ewnakgBQ_7kvDvOGCqhT3I8n9qIZkhCIETS57olThSo/edit#gid=1419095983">download tại đây</a></p>
                        </div>
                    </div>
            @endif
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
            @if(isset($dataExcel) && count($dataExcel))
                @if(count($dataExcel)>0)
                    <div class="form-group">
                        <div class="alert alert-primary">
                            <ul>
                                <li>Có {{count($dataExcel)}} bản ghi</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="alert alert-primary">
                            <div class="form-row">
                                <div class="col-md-3">Tổng thu nhập trước thuế</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_thu_nhap_truoc_thue'])}} đ</b>
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
                        </div>
                    </div>
                @endif
                @if(isset($dataWarning) && $dataWarning)
                    <div class="form-group">
                        <div class="alert alert-warning" style="max-height: 200px;overflow: scroll">
                            <ul class="list-unstyled">
                                @if(isset($dataWarning['empRent'])&&$dataWarning['empRent'])

                                    <li>
                                        <p>Nhân sự thuê khoán sẽ tự động thêm vào hệ thống</p>
                                        <ul>
                                            @foreach($dataWarning['empRent'] as $empWarning)
                                                <li>{!! $empWarning !!}</li>
                                            @endforeach
                                        </ul>
                                    </li>

                                @endif
                                @if(isset($dataWarning['warning'])&&$dataWarning['warning'])

                                    <li>
                                        <p>Cảnh báo</p>
                                        <ul>
                                            @foreach($dataWarning['warning'] as $empWarning)
                                                <li>{!! $empWarning !!}</li>
                                            @endforeach
                                        </ul>
                                    </li>

                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
                @if(isset($dataError) && $dataError)
                    <div class="form-group">
                        <div class="alert alert-danger" style="max-height: 200px;overflow: scroll">
                            <ul>
                                @foreach($dataError as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="form-group" style="display: none" id="showErrorTable">

                </div>
                @if(isset($dataExcel) && $dataExcel)
                    <div class="form-group">
                        <div class="dropdown pull-right" style="margin-bottom: 10px" id="dd-visabale-col">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Chọn cột
                                hiển thị
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                 x-placement="top-start"
                                 style="max-height: 500px;overflow-x: scroll;top:100px;width: 700px;padding: 10px">
                                <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px">Chọn trường hiển
                                    thị:
                                    <span
                                            class="btnChooseTable btnChooseTableChecked"
                                            id="btnChooseAll">Chọn tất cả</span> | <span class="btnChooseTable"
                                                                                         id="btnCustom">Tùy chỉnh</span>
                                </div>
                                <div>
                                    @if($isSalary)
                                        @foreach($dataTable as $col)
                                            <a class="dropdown-item choose_col"
                                               style="width:48%;display: inline-block;word-break: break-all;white-space: normal;"
                                               href="javascript:void(0)">
                                                <input class="form-check-input"
                                                       data-visable="{{$col['visible']}}"
                                                       value="{{$col['field']}}" type="checkbox"
                                                       @if($col['visible'])checked="checked"@endif>
                                                <span>{{$col['title']}}</span>
                                            </a>

                                        @endforeach
                                    @else
                                        @foreach(renderTableColTabulatorFTT() as $col)
                                            @if(!$col['required'])
                                                <a class="dropdown-item choose_col"
                                                   style="width:48%;display: inline-block;word-break: break-all;white-space: normal;"
                                                   href="javascript:void(0)">
                                                    <input class="form-check-input"
                                                           data-visable="{{$col['visible']}}"
                                                           value="{{$col['field']}}" type="checkbox"
                                                           @if($col['visible'])checked="checked"@endif>
                                                    <span>{{$col['title']}}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="salary-table" class="tabulator table-bordered"></div>
                    </div>
                    <input type="hidden" name="dataTable" value="">
                    @if($isSalary)
                        <button type="button" class="btn btn-primary" id="btnSave"
                                @if(isset($dataError) && $dataError) disabled
                                @endif data-action="{{route('order.import.save')}}">
                            Lưu
                        </button>
                    @else
                        @if(isset($dataWarning['empRent'])&&$dataWarning['empRent'])
                            <button type="button" class="btn btn-primary" id="btnSave" data-hasnewemp="true"
                                    @if(isset($dataError) && $dataError) disabled
                                    @endif data-action="{{route('order.import.ftt.save')}}">
                                Lưu
                            </button>
                        @else
                            <button type="button" class="btn btn-primary" id="btnSave" data-hasnewemp="false"
                                    @if(isset($dataError) && $dataError) disabled
                                    @endif data-action="{{route('order.import.ftt.save')}}">
                                Lưu
                            </button>
                        @endif

                    @endif

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
    <link href="{{URL::asset('assets/css/tabulator-boot.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{URL::asset('assets/js/tabulator.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#phap_nhan').prop('disabled', true);
            @if(isset($dataExcel)&&$dataExcel)
            $('#phap_nhan').prop('disabled', true);
            $('#month').prop('disabled', true);
            $('#year').prop('disabled', true);

            @endif



            $('#btnUpload').click(function () {
                if (validate()) {
                    $('#loader').removeClass('fadeOut').addClass('fadeIn').css('opacity', 0.5);

                    $('#form-import').submit();
                }
            });
            $('#btnBack').click(function (e) {
                var dataTable = $("#salary-table").tabulator("getData");
                $('input[name=dataTable]').val(JSON.stringify(dataTable));
                $('#form-import').attr('action', $(this).data('action'));
                $('#form-import').submit();
            });

            $('#btnCancel').click(function (e) {
                if (!confirm("Dữ liệu của bạn sẽ bị mất nếu ấn Cancel")) {
                    e.preventDefault();
                }
            });
            $('#btnSave').click(function (e) {
                if (validate(true)) {
                    @if($isSalary)
                    checkPhapNhanMonthYear();
                    @else
                        isNewEmp = $(this).data('hasnewemp');
                    console.log(isNewEmp);
                    if (isNewEmp) {
                        if (confirm('Có nhân sự thuê khoán chưa tồn tại trong hệ thống. Bạn có muốn lưu lại không?')) {
                            $('#form-import').attr('action', $('#btnSave').data('action'));
                            $('#form-import').submit();
                        }

                    } else {
                        $('#form-import').attr('action', $('#btnSave').data('action'));
                        $('#form-import').submit();
                    }

                    @endif
                }
            });
            // $.notify("Hello World");

            $('#btnBack').click(function (e) {
                var dataTable = $("#salary-table").tabulator("getData");
                $('input[name=dataTable]').val(JSON.stringify(dataTable));
                $('#form-import').attr('action', $(this).data('action'));
                $('#form-import').submit();
            });
            $('.dropdown-item.choose_col').click(function (e) {
                e.stopPropagation();
                statusCheckbox = $(this).find('input[type=checkbox]').prop('checked');
                if (e.target.tagName != 'INPUT') {
                    checkboxIsDisabled = $(this).find('input[type=checkbox]').prop('disabled');
                    if (!checkboxIsDisabled) {
                        if (statusCheckbox) {
                            $(this).find('input[type=checkbox]').prop('checked', false);
                            $(this).find('input[type=checkbox]').data('visable', false);
                        } else {
                            $(this).find('input[type=checkbox]').prop('checked', true);
                            $(this).find('input[type=checkbox]').data('visable', true);
                        }
                    }
                } else {
                    $(this).find('input[type=checkbox]').data('visable', statusCheckbox);
                }
                renderTable();
            });
            $('#btnChooseAll').click(function (e) {
                e.stopPropagation();

                if ($(this).hasClass('btnChooseTableChecked')) {
                    $('#dd-visabale-col input[type=checkbox]').each(function () {
                        $(this).prop('checked', true);
                    });
                    // đóng tất cả các checkbox
                    $('#dd-visabale-col input[type=checkbox]').each(function () {
                        $(this).prop('disabled', true);
                        $(this).data('isDisabled', true);
                    });
                }


                $('.btnChooseTable').removeClass('btnChooseTableChecked');
                $('#btnCustom').addClass('btnChooseTableChecked');
                renderTable();
            });

            $('#btnCustom').click(function (e) {
                e.stopPropagation();
                if ($(this).hasClass('btnChooseTableChecked')) {
                    $('#dd-visabale-col input[type=checkbox]').each(function () {
                        $(this).prop('checked', false);
                        var visibleCol = $(this).data('visable');
                        if (visibleCol) {
                            $(this).prop('checked', true);
                        }
                        // mở tất cả các checkbox
                        $('#dd-visabale-col input[type=checkbox]').each(function () {
                            $(this).prop('disabled', false);
                            $(this).data('isDisabled', false);
                        });
                    });
                }

                $('.btnChooseTable').removeClass('btnChooseTableChecked');
                $('#btnChooseAll').addClass('btnChooseTableChecked');
                renderTable();
            });

        });

        function renderTable() {
            var allVals = [];
            var checkedVals = [];
            $('#dd-visabale-col input[type=checkbox]').each(function () {
                allVals.push($(this).val());
            });
            $('#dd-visabale-col input[type=checkbox]:checked').each(function () {
                checkedVals.push($(this).val());
            });
            $.each(allVals, function (k, v) {
                $("#salary-table").tabulator("hideColumn", v) //hide the "name" column
            })
            $.each(checkedVals, function (k, v) {
                $("#salary-table").tabulator("showColumn", v) //hide the "name" column
            })
        }

        // Kiểm tra dữ liệu pháp nhân trong tháng X đã import hay chưa?
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
                        $('#form-import').attr('action', $('#btnSave').data('action'));
                        $('#form-import').submit();
                    }

                    if (r.status == 0) {
                        if (confirm(r.message)) {
                            $('#form-import').attr('action', $('#btnSave').data('action'));
                            $('#form-import').submit();
                        }

                    }
                }
            });
        }

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
            @if($isSalary)
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
            @endif

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
                    @if(isset($dataExcel)&&$dataExcel)
                    $('#month').prop('disabled', false);
                    $('#year').prop('disabled', false);
                    $('#btnSave').prop('disabled', true);
                    @endif
                }
            }
        }

        @if(isset($dataExcel) )
        // set data table
        //define some sample data
        var checkNotNull = function (cell, value, parameters) {
            //cell - the cell component for the edited cell
            //value - the new input value of the cell
            //parameters - the parameters passed in with the validator

            console.log('ssss');
        }
        @if($isSalary)
        $('#infoEmployee').on('hide.bs.modal', function () {
            // $('body').css('padding-right','0');
        });
        $('#infoEmployee').on('resize.bs.modal', function () {
            // $('body').css('padding-right','0');
        });
        {{--@php(dd($dataTable))--}}
        $("#salary-table").tabulator({
            columns:<?= json_encode($dataTable)?>,
            pagination: "local",
            paginationSize: 50,
            cellEdited: function (cell) { //trigger an alert message when the row is clicked

            },
            rowClick: function (e, row) {
                // $('#infoEmployee').modal('show');
                // $('#infoEmployee').modal('toggle')
                $('#infoEmployee .modal-body').html(row.getData().data);
                $('#btnModel').trigger('click');
            },
            rowUpdated: function (row) {
                // var data = row.getData(); //get data object for row
                // console.log(data);
                // if(data.employee_id == null){
                //     row.getElement().css({"background-color":"red"}); //apply css change to row element
                //     row.getElement().css({"color":"white"}); //apply css change to row element
                // }
            },
            rowFormatter: function (row) {
                var data = row.getData(); //get data object for row
                if (data.cssClass != undefined) {
                    row.getElement().addClass(data.cssClass);
                }
            },
            tableBuilt: function () {
                cell = $('.tabulator-calcs-top .tabulator-cell[tabulator-field=thu_nhap_truoc_thue]').html();
                console.log(cell);
            },
        });
        @else
        $("#salary-table").tabulator({
            columns:<?= json_encode(renderTableColTabulatorFTT())?>,
            pagination: "local",
            paginationSize: 50,
            cellEdited: function (cell) { //trigger an alert message when the row is clicked

            },
            rowUpdated: function (row) {
                // var data = row.getData(); //get data object for row
                // console.log(data);
                // if(data.employee_id == null){
                //     row.getElement().css({"background-color":"red"}); //apply css change to row element
                //     row.getElement().css({"color":"white"}); //apply css change to row element
                // }
            },
            rowFormatter: function (row) {
                var data = row.getData(); //get data object for row
                if (data.cssClass != undefined) {
                    row.getElement().addClass(data.cssClass);
                }
            },
            tableBuilt: function () {
                cell = $('.tabulator-calcs-top .tabulator-cell[tabulator-field=thu_nhap_truoc_thue]').html();
                console.log(cell);
            },
        });
        @endif


        //define some sample data
        var tabledata = <?php echo isset($dataExcel) ? (json_encode($dataExcel)) : "" ?>;
        console.log(tabledata);
        //load sample data into the table
        $("#salary-table").tabulator("setData", tabledata);



        @endif
    </script>
@endsection