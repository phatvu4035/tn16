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
    </style>

    <div class="row gap-20 masonry pos-r">
        <div class="masonry-sizer col-md-12"></div>
        <div class="masonry-item col-md-12">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <a class="btn btn-primary" href="{{route('cross_check.listCrossCheckYear', [
                        'pre-load-pn' => $phap_nhan
                    ])}}">Quay lại</a>
                </div>
            </div>
            {{Form::openForm('Import dữ liệu đối soát ',['method'=>'POST', 'id'=>'form-import','files' => true])}}
            <div class="form-row">
                <div class="form-group">
                    <label class="fw-500" style="padding-right: 20px">Chọn file (.xlsx, xls)</label>
                    <input type="file" id="importFile" name="importFile" onchange="checkFileEmpty()">
                    <button type="button" class="btn btn-primary" id="btnUpload" disabled><i class="fa fa-download"></i> Import</button>
                    <a href="/assets/templateExcel/Mau334.xlsx" class="btn btn-primary"><i class="fa fa-download"></i> Tải mẫu 334</a>
                </div>
            </div>
            @if(isset($dataExcel))
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
                                <div class="col-md-9"><b>{{number_format($total['tong_tn_truoc_thue'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Tổng TN không chịu thuế</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_non_tax'])}} đ</b></div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-3">Tổng TNCN</div>
                                <div class="col-md-9"><b>{{number_format($total['tong_tncn'])}} đ</b></div>
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

                @if(isset($dataError) && $dataError)
                    <div class="form-group">
                        <div class="alert alert-danger">
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
                                 style="height: 200px;overflow-x: scroll;top:100px;width: 700px;padding: 10px">
                                <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px">Chọn trường hiển
                                    thị:
                                    <span
                                            class="btnChooseTable btnChooseTableChecked"
                                            id="btnChooseAll">Chọn tất cả</span> | <span class="btnChooseTable"
                                                                                         id="btnCustom">Tùy chỉnh</span>
                                </div>
                                <div>
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
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="salary-table" class="tabulator table-bordered"></div>
                    </div>
                    <input type="hidden" name="dataTable" value="">
                    <button type="button" class="btn btn-primary" id="btnSave"
                            @if(isset($dataError) && $dataError) disabled
                            @endif data-action="{{route('order.import.ftt.save')}}">
                        Save
                    </button>
                    <a href="{{route('order.create')}}" class="btn btn-danger" id="btnCancel">Cancel</a>
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
@endsection
@section('script')
    <link href="{{URL::asset('assets/css/tabulator-boot.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{URL::asset('assets/js/tabulator.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#btnUpload').click(function () {
                if (validate()) {
                    $('#form-import').submit();
                }
            });
            $('#btnCancel').click(function (e) {
                if (!confirm("Dữ liệu của bạn sẽ bị mất nếu ấn Cancel")) {
                    e.preventDefault();
                }
            });
            $('#btnSave').click(function (e) {
                var dataTable = $("#salary-table").tabulator("getData");
                $('input[name=dataTable]').val(JSON.stringify(dataTable));
                $('#form-import').attr('action', $('#btnSave').data('action'));
                $('#form-import').submit();
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

        function validate() {
            var check = true;
            if ($('#importFile').val() == '') {
                check = false;
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
                if (isExcel)
                    $('#btnUpload').attr('disabled', false);
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

        $("#salary-table").tabulator({
            columns:<?= json_encode(renderTableColTabulatorFTT())?>,
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

        //define some sample data
        var tabledata = <?php echo isset($dataExcel) ? (json_encode($dataExcel)) : "" ?>;
        //load sample data into the table
        $("#salary-table").tabulator("setData", tabledata);



        @endif
    </script>
@endsection