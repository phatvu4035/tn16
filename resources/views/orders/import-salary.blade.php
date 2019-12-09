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
            <button class="btn btn-primary" id="btnBack" data-action="{{route('post.order.create.salary')}}">Quay lại
            </button>
        </div>
        <div class="masonry-item col-md-12">
            {{Form::openForm('Import bảng lương',['method'=>'POST','route'=>'order.import.file','id'=>'form-import','files' => true])}}
            <input type="hidden" name="order" value="{{json_encode($returnData['order'])}}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    {{ Form::inputField([
                                    'label' => "Pháp nhân *",
                                    'name' => 'phap_nhan',
                                    'options' => [
                                        'placeholder' => '',
                                        'class' => 'form-group'                                    ],
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
                            'selected'=>isset($returnData['import']['month'])?$returnData['import']['month']:0,
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
                <div class="form-group">
                    <label class="fw-500" style="padding-right: 20px">Chọn file bảng lương (.xlsx, xls)</label>
                    <input type="file" id="importFile" name="importFile" onchange="checkFileEmpty()">
                    <button type="button" class="btn btn-primary" id="btnUpload" disabled>Import
                    </button>
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
                <div class="form-group">
                    <div class="dropdown pull-right" style="margin-bottom: 10px" id="dd-visabale-col">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Chọn cột hiển thị
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start"
                             style="height: 200px;overflow-x: scroll;top:100px;width: 700px;padding: 10px">
                            <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px">Chọn trường hiển thị: <span
                                        class="btnChooseTable btnChooseTableChecked"
                                        id="btnChooseAll">Chọn tất cả</span> | <span class="btnChooseTable"
                                                                                     id="btnCustom">Tùy chỉnh</span>
                            </div>
                            <div>
                                @foreach(renderTableColTabulator() as $col)
                                    @if(!$col['required'])
                                        <a class="dropdown-item choose_col"
                                           style="width:48%;display: inline-block;word-break: break-all;white-space: normal;"
                                           href="javascript:void(0)">
                                            <input class="form-check-input" data-visable="{{$col['visible']}}"
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
                <button type="button" class="btn btn-primary" id="btnSave" @if(isset($dataError) && $dataError) disabled
                        @endif data-action="{{route('order.import.save')}}">
                    Save
                </button>
                <a href="{{route('order.create.salary')}}" class="btn btn-danger" id="btnCancel">Cancel</a>
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
                if (validateBeforeSave()) {
                    checkPhapNhanMonthYear();

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

        function validate() {
            var check = true;
            if ($('#importFile').val() == '') {
                check = false;
            }
            return check;
        }

        function validateBeforeSave() {

            //check datatable
            var check = true;
            var dataTable = $("#salary-table").tabulator("getData");
            if (dataTable.length <= 0) {
                check = check && false;
                var html = "<div class=\"alert alert-danger\">\n" +
                    "                        <ul>\n" +
                    "                           <li>File Excel không có dữ liệu</li>\n" +
                    "                        </ul>\n" +
                    "                    </div>"
                $('#showErrorTable').html(html);
                $('#showErrorTable').css('display', 'block');

                //fix height of main content
                var h = $('#showErrorTable').height() + $('#mainContent>.row').height();
                $('#mainContent>.row').height(h);
            }
            // check phap nhan
            var phap_nhan = $('#phap_nhan').val();
            if (!phap_nhan) {
                check = check && false;
                $('#phap_nhan').addClass('errors_style');
                // $('#small-phap_nhan1').html('Pháp nhân không được trống').removeClass('text-muted').css('color', 'red');
            }
            // check năm
            var year = $('#year').val();
            if (!year) {
                check = check && false;
                $('#year').addClass('errors_style');
                // $('#small-year1').html('Năm không được trống').removeClass('text-muted').css('color', 'red');
            }
            // check tháng
            var month = $('#month').val();
            if (!month) {
                check = check && false;
                $('#month').addClass('errors_style');
                // $('#small-year1').html('Năm không được trống').removeClass('text-muted').css('color', 'red');
            }
            return check;
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
                        var dataTable = $("#salary-table").tabulator("getData");
                        $('input[name=dataTable]').val(JSON.stringify(dataTable));
                        $('#form-import').attr('action', $('#btnSave').data('action'));
                        $('#form-import').submit();
                    }

                    if (r.status == 0) {
                        confirm(r.message);
                    }
                }
            });
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

        @if(isset($dataExcel))
        // set data table
        //define some sample data
        var checkNotNull = function (cell, value, parameters) {
            //cell - the cell component for the edited cell
            //value - the new input value of the cell
            //parameters - the parameters passed in with the validator

            console.log('ssss');
        }

        $("#salary-table").tabulator({
            layout: "fitDataFill",
            columns:<?= json_encode(renderTableColTabulator())?>,
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
        });
        //define some sample data
        var tabledata = <?php echo isset($dataExcel) ? (json_encode($dataExcel)) : '' ?>;
        //load sample data into the table
        $("#salary-table").tabulator("setData", tabledata);

        @endif
    </script>
@endsection