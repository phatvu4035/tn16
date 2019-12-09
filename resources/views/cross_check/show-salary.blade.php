@extends('layouts.app')

@section('content')
<div class="masonry-item col-md-12 w-100" id="show-cross-check">
    <div class="bgc-white p-20 bd">
        <h3 class="c-grey-900">Kết quả đối soát lương tháng {{$thang}}</h3>
        <div class="mT-30">
            <div class="row">
                <div class="col col-md-2"><b>Pháp nhân :</b></div>
                <div class="col col-md-3">{{$phap_nhan}}</div>
            </div>
            <div class="row">
                <div class="col col-md-2"><b>Tháng</b></div>
                <div class="col col-md-3">{{$thang}}</div>
            </div>
            <div class="row">
                <div class="col col-md-12 text-center">
                    <div id="cross-check-table-salary" class="tabulator table-bordered"></div>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12">
                    <div class="modal fade" id="confirm-done" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Hoàn thành đối soát
                                </div>
                                <div class="modal-body" id="body-done">
                                    <div class="row">
                                        <div class="col col-md-6">Nhập thời gian thanh toán</div>
                                        <div class="col col-md-6 text-right"><button class="btn btn-primary" id="clone"><b>+</b></button></div>
                                    </div>
                                    <div class="row cloneable">
                                        <div class="col col-md-2" style="line-height: 28px;">Đợt</div>
                                        <div class="col col-md-4">
                                        {{ Form::inputField([
                                            'label' => null,
                                            'name' => 'ngay_thanh_toan[]',
                                            'options' => [
                                                    'placeholder' => 'Nhập ngày thanh toán.',
                                                    'placeholder' => '',
                                                    'class' => 'form-group start-date ngay_thanh_toan',
                                                    'data-provide'=>"datepicker"
                                                ],
                                            ])
                                        }}
                                        </div>
                                        <div class="col col-md-3">
                                            <button class="btn btn-danger d-none remove-ngay"><b>x</b></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="dismiss-cancel-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy
                                    </button>
                                    <button id="done-check" class="btn btn-success">Hoàn thành</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(\App\Facades\Topica::canCross("export.cross_check_kt_check", $phap_nhan))
                        @if (!$isDone)
                            <button class="confirm btn btn-success" id="showDoneModal" data-toggle='modal' data-target='#confirm-done'><i
                                        class="fa fa-check"></i> Kế toán xác nhận hoàn thành đối soát</button>
                        @endIf
                    @endIf
                    <button class="btn btn-danger" data-toggle='modal' data-target='#confirm-cancel'><i
                                class="fa fa-remove"></i> Hủy bộ đối soát
                    </button>
                    <a href="/doi-soat/export/luong/{{$phap_nhan}}/{{$thang}}/{{$nam}}" class="btn btn-primary" id="export-xlsx"><i class="fa fa-save"></i> Xuất báo cáo</a>
                    <div class="modal fade" id="confirm-cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Hủy đối soát
                                </div>
                                <div class="modal-body">
                                    <p>Bạn có chắc chắn muốn hủy bỏ đối soát cho pháp nhân <b>{{$phap_nhan}}</b> tháng {{$thang}}
                                        năm {{$nam}} ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="dismiss-cancel-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy
                                    </button>
                                    <a href="/doi-soat/cancel-cross-check/{{$info[0]['id']}}" cross-id="{{$info[0]['id']}}"
                                       id="confirm-cancel-btn" class="btn btn-danger">Đồng ý</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endSection()
@section("script")
<script>
    var data = '<?php echo $sumSummaries ?>';
    $("#clone").click(function (e) {
        var ele = $(".cloneable").first().clone();

        $(ele).find(".ngay_thanh_toan").val("");
        $(ele).find(".d-none").removeClass("d-none");
        $("#body-done").append(ele);

        $(ele).find(".remove-ngay").click(function (e) {
            $(ele).remove();
        });
    });

    $("#done-check").click(function (e) {
        var that = e.target;
        var dateElems = $("input.ngay_thanh_toan");
        var dates = [];
        var loop = true;
        dateElems.each(function(k,v ){
            if ($(v).val() !== "") {
                dates.push($(v).val());
            } else {
                $(v).focus();
                makeAlert("Thất bại", "Bạn chưa nhập thời gian thanh toán", "danger");
                loop = false;
                return false;
            }
        });

        if (!loop) {
            return;
        }

        $(that).attr("disabled", true);

        var params = {
            thang : "{{$thang}}",
            nam : "{{$nam}}",
            phap_nhan : "{{$phap_nhan}}",
            is_salary : 1,
            dates : dates
        };

        $.ajax({
            url: "{{route("cross_check.doneSalary")}}",
            method: "POST",
            data: params,
            beforeSend: function (request) {
                request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            },
            success: function (e) {
                makeAlert(e.title, e.content, e.type);
                if (e.type == 'success') {
                    $("#showDoneModal").hide();
                }
            },
            error: function (e) {
                makeAlert("Thất bại", "Đã có lỗi xảy ra", "danger");
            },
            complete: function (e) {
                $("#dismiss-cancel-btn").click();
                $("#dismiss-cancel-btn").click();
                $(that).removeAttr("disabled");
            }
        });
    });

    $("#cross-check-table-salary").tabulator({
        height: "", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
        layout: "fitData", //fit columns to width of table (optional)
        columnVertAlign: "bottom",
        layoutColumnsOnNewData:true,
        columns: [ //Define Table Column
            {
                title: "#", headerSort: false, field: "idx", formatter: function (cell, formatterParams) {
                    return cell.getRow().getPosition(true) + 1;
                }
            },
            {
                title: "Nội dung",
                field: "noi_dung",
            },
            {
                title: "Tổng TN trước thuế",
                field: "sum_thu_nhap_truoc_thue",
                formatter: "money",
                formatterParams: {precision: 0}
            },
            {
                title: "Thu nhập không chịu thuế",
                field: "sum_non_tax",
                formatter: "money",
                formatterParams: {precision: 0}
            },
            {
                title: "Tổng thu nhập chịu thuế",
                mutator:function(value, data, type, params, component){
                    //value - original value of the cell
                    //data - the data for the row
                    //type - the type of mutation occuring (data|edit)
                    //params - the mutatorParams object from the column definition
                    //component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column

                    return data.sum_thu_nhap_truoc_thue - data.sum_non_tax; //return the new value for the cell data.
                },
                formatter: "money",
                formatterParams: {precision: 0}
            },
            {
                title: "Bảo hiểm xã hội nhân viên đóng",
                field: "sum_bhxh",
                formatter: "money",
                formatterParams: {precision: 0}
            },
            {
                title: "Thuế",
                columns : [
                    {
                        title: "Tổng thuế TNCN",
                        field: "sum_thue_tam_trich",
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                    {
                        title: "Thuế TNCN đã trích",
                        field: "thue_da_trich",
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                    {
                        title: "Thuế TNCN bổ sung",
                        field: "thue_tam_trich",
                        mutator:function(value, data, type, params, component){
                            //value - original value of the cell
                            //data - the data for the row
                            //type - the type of mutation occuring (data|edit)
                            //params - the mutatorParams object from the column definition
                            //component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column

                            return data.sum_thue_tam_trich - data.thue_da_trich; //return the new value for the cell data.
                        },
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                ]
            },
            {
                title: "Thực nhận",
                columns : [
                    {
                        title: "Tổng Thực nhận",
                        field: "sum_thuc_nhan",
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                    {
                        title: "Đã thanh toán",
                        field: "da_thanh_toan",
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                    {
                        title: "Còn lại",
                        field: "con_lai_can_thanh_toan",
                        formatter: "money",
                        formatterParams: {precision: 0}
                    },
                ]
            },
            {
                title: "Bộ thanh toán",
                field: "order_id",
                mutator:function(value, data, type, params, component){
                    //value - original value of the cell
                    //data - the data for the row
                    //type - the type of mutation occuring (data|edit)
                    //params - the mutatorParams object from the column definition
                    //component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column

                    return "F-"+value+" ("+data.serial+")"; //return the new value for the cell data.
                },
            },
        ]
    });
    $("#cross-check-table-salary").tabulator('setData', data);
</script>
@endSection()