@extends('layouts.app')
@section('custom-css')
    <style>
        .tabulator .tabulator-header .tabulator-col .tabulator-col-content .tabulator-col-title {
            white-space: normal;
        }

        div#cross-check-year-table * {
            font-size: 12px !important;
        }
        .accounter_column {
            background: rgba(43, 79, 218, 0.17) !important;
        }
        .TCB_column {
            background: rgba(83, 218, 66, 0.17) !important;
        }
        .compare_column {
            background: rgba(218, 39, 154, 0.17) !important;
        }
        input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: searchfield-cancel-button;
        }
        .year-found {
            color : #0f9aee;
        }
        .year-conflict {
            color: rgb(240, 173, 78);
        }
        .tabulator .tabulator-header .tabulator-calcs-holder {
            width: 200%;
        }
        .tabulator-calcs-top .tabulator-cell {
            border-left: 1px solid #ddd;
            border-top: none;
        }
    </style>
@endSection()
@section('content')
    <div class="masonry-item col-md-12 w-100" id="show-cross-check">
        <div class="form-row">
            <div class="form-group col-md-6">
                <a class="btn btn-primary" href="{{route('cross_check.listCrossCheckYear', [
                        'pre-load-pn' => $phap_nhan
                    ])}}">Quay lại</a>
            </div>
        </div>
        <div class="bgc-white p-20 bd">
            <h3 class="c-grey-900">Đối soát cho pháp nhân {{$phap_nhan}} năm {{$nam}}</h3>
            <div class="mT-30">
                <div class="row">
                    <div class="col col-md-10"></div>
                    <div class="col col-md-2 text-right">
                        {{
                            Form::dropDown([
                                'name' => 'filter-select',
                                'data' => [
                                    "Hiển thị tất cả" => "all",
                                    "Khớp" => "khop",
                                    "Lệch" => "lech",
                                    "Bỏ qua"=> "bo-qua"
                                ],
                                'noDefault' => true,
                                'options' => [
                                    'id' => 'filter-select',
                                    'class' => 'filter-select',
                                    'required' => true
                                ],
                            ])
                        }}
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-12 text-center">
                        <div id="cross-check-year-table" class="tabulator table-bordered"></div>
                    </div>
                    <div class="col col-md-12 text-center">
                        <div id="pagination-cross-check-year-table" style="margin-top: 10px" class="align-center tabulator-pagination"></div>
                    </div>
                </div>
                <div class="form-row" style="margin-top: 10px;">
                    <div class="col col-md-12">
                        <?php
                        $doneUrl = route("cross_check.doneAccounterYear", [
                            "cross_id" => $cci['0']['id']
                        ]);
                        ?>
                        @if(\App\Facades\Topica::canCross("export.cross_check_kt_check", $phap_nhan))
                        <a href="{{$doneUrl}}" id="done-accounter" class="btn btn-success d-none"><i
                                    class="fa fa-check"></i> Kế toán xác nhận hoàn thành đối soát</a>
                        @endIf()
                            <?php
                            $createUrl = route("order.create", [
                                "temp_order" => "true",
                                "cross_check_info_id" => $cci[0]['id']
                            ]);
                            ?>
                        <a href="{{ route('cross_check.exportYear', [$phap_nhan, $nam]) }}" class="btn btn-primary" id="export-xlsx"><i class="fa fa-save"></i> Xuất báo cáo
                    </a>
                        <a class="btn btn-primary" href="{{$createUrl}}"><i class="fa fa-plus"></i> Bổ sung bộ thanh toán</a>
                        <button class="btn btn-danger" data-toggle='modal' data-target='#confirm-cancel'><i
                                    class="fa fa-remove"></i> Hủy bộ đối soát
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm-set-active" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Bỏ qua đối soát
                </div>
                <div class="modal-body">
                    <p id="set-active-text">Bạn có chắc chắn bỏ qua đối soát cho khoản này ?</p>
                    <div class="row">
                        <div class="col col-md8">
                            <div class="reason">
                                <?=
                                Form::inputTextarea([
                                    'label' => 'Lý do bỏ qua <span class="c-r">*</span>',
                                    'name' => 'reason',
                                    'value' => '',
                                    'options' => [
                                        'placeholder' => 'Điền lý do bỏ qua đối soát cho khoản này',
                                        'id' => 'reason'
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="dismiss-cancel-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy
                    </button>
                    <button cross-id="0" active="0" id="confirm-set-active-btn" class="btn btn-danger">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
    <button id="showSetActiveModal" style="display: none" data-toggle='modal' data-target='#confirm-set-active'></button>
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Hủy liên kết bộ thanh toán
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy bỏ liên kết cho bộ thanh toán này ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="dismiss-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy</button>
                    <button data-order-id="" id="confirm-delete-btn" class="btn btn-danger">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
    <button id="showDeleteModal" style="display: none" data-toggle='modal' data-target='#confirm-delete'></button>
    <div class="modal fade" id="confirm-cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Hủy đối soát
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy bỏ đối soát cho pháp nhân <b>{{$phap_nhan}}</b> năm {{$nam}} ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="dismiss-cancel-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy
                    </button>
                    <a href="/doi-soat/cancel-cross-check/{{$cci[0]['id']}}?crossYear=true" cross-id="{{$cci[0]['id']}}"
                       id="confirm-cancel-btn" class="btn btn-danger">Đồng ý</a>
                </div>
            </div>
        </div>
    </div>
@endSection()
@section("script")
<script>
    var ke_toan_check = false;
    var topCalc = function(values, data, calcParams){
        //values - array of column values
        //data - all table data
        //calcParams - params passed from the column defintion object

        var calc = 0;
        values.forEach(function(v, k){
            if(data[k].active == 1){
                calc += v;
            }
        });

        return calc;
    }

    $("#cross-check-year-table").tabulator({
        ajaxURL: "/doi-soat/bo-doi-soat-nam/{{$phap_nhan}}/{{$nam}}", //ajax URL
        ajaxParams: ajaxParams, //ajax parameters
        ajaxConfig: {
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }, //ajax HTTP request type
        height: "100%", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
        layout: "fitData", //fit columns to width of table (optional)
        pagination: "local",
        paginationSize: 20,
        persistenceMode:"local",
        persistentSort:true,
        ajaxLoader: false,
        columnVertAlign: "bottom",
        // ajaxLoaderLoading: '<div class="loader"></div>',
        paginationElement: $("#pagination-cross-check-year-table"),
        columns: [ //Define Table Columns
            {
                title: "#", headerSort: false, field: "idx", formatter: function (cell, formatterParams) {
                    //cell - the cell component
                    //formatterParams - parameters set for the column
                    return cell.getRow().getPosition(true) + 1;
                }
            },
            {title: "preventShow", field: "id", visible: false},
            {title: "active", field: "active", visible: false, formatter: function (cell, formatterParams) {
                    var row = cell.getRow();
                    if (cell.getValue() == 0) {
                        row.getElement().css({"opacity": 0.5});
                    }
                    return cell.getValue();
            }},
            {
                title: "Ngày chứng từ",
                field: "ngay_chung_tu",
                formatter: function (cell, formatterParams) {
                    //cell - the cell component
                    //formatterParams - parameters set for the column
                    if (cell.getValue() == undefined) {
                        console.log("123");
                    }
                    return dateFormatter(cell.getValue());
                },
                width: 140
            },
            {title: "Mã CT", field: "ma_chung_tu", width: 90},
            {title: "Số chứng từ", field: "so_chung_tu", visible: false},
            {title: "Mã khách", field: "ma_khach", visible: true, width: 110},
            {title: "Tên khách", field: "ten_khach", visible: false},
            {
                title: "Diễn giải", field: "dien_giai", formatter: function (cell, formatterParams) {
                    //cell - the cell component
                    //formatterParams - parameters set for the
                    try {
                        var data = JSON.parse(cell.getValue());
                    }
                    catch(err) {
                        var data = cell.getValue();
                    }

                    return data.join("<br>");
                }
            },
            {title: "Tài khoản Đ.Ứ", field: "tai_khoan_doi_ung", visible: false},
            {
                title: "PS nợ",
                field: "ps_no",
                visible: false,
                formatter: "money",
                formatterParams: {precision: 0}
            },
            {
                title: "Năm",
                columns: [
                    {
                        title: "Thu nhập <br>",
                        cssClass: "accounter_column",
                        visible: true,
                        field: "nam.thu_nhap",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thuế <br>",
                        cssClass: "accounter_column",
                        visible: true,
                        field: "nam.thue",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thực nhận <br>",
                        cssClass: "accounter_column",
                        visible: true,
                        field: "nam.thuc_nhan",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                ]
            },
            {
                title: "Tháng",
                columns: [
                    {
                        title: "Thu nhập <br>",
                        cssClass: "TCB_column",
                        visible: true,
                        field: "thang.thu_nhap",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thuế <br>",
                        cssClass: "TCB_column",
                        visible: true,
                        field: "thang.thue",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thực nhận <br>",
                        cssClass: "TCB_column",
                        visible: true,
                        field: "thang.thuc_nhan",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                ]
            },
            {
                title: "Chênh lệch",
                columns: [
                    {
                        title: "Thu nhập <br>",
                        cssClass: "compare_column",
                        visible: true,
                        field: "chenh_lech.thu_nhap",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thuế <br>",
                        cssClass: "compare_column",
                        visible: true,
                        field: "chenh_lech.thue",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                    {
                        title: "Thực nhận <br>",
                        cssClass: "compare_column",
                        visible: true,
                        field: "chenh_lech.thuc_nhan",
                        formatter: "money",
                        formatterParams: {precision: 0},
                        topCalc: topCalc,
                        topCalcFormatter: "money",
                        topCalcFormatterParams:{precision: 0},
                    },
                ]
            },
            {
                title: "Bộ thanh toán <br> gốc",
                field: "order_id",
                formatter: function(cell) {
                    var value = cell.getValue();
                    var data = cell.getRow().getData();
                    var done = data.chenh_lech.thuc_nhan == 0 && data.chenh_lech.thue == 0 && data.chenh_lech.thu_nhap == 0;
                    if (!done && data.additional_order) {
                        done = true;
                    }

                    if (data.order_id == null) {
                        return "Không thấy";
                    } else {
                        var text = $("<span>").addClass("year-found").html("F-" + value + "(" + data.order.serial + ")").clone();
                        if (!done) {
                            text.removeClass("year-found");
                            text.addClass("year-conflict");
                        }
                        return text.prop('outerHTML');
                    }
                },
                frozen: true,
            },
            {
                title: "Bộ thanh toán <br> bổ sung",
                field: "additional_order",
                sorter:function(a, b, aRow, bRow, column, dir, sorterParams){
                    //a, b - the two values being compared
                    //aRow, bRow - the row components for the values being compared (useful if you need to access additional fields in the row data for the sort)
                    //column - the column component for the column being sorted
                    //dir - the direction of the sort ("asc" or "desc")
                    //sorterParams - sorterParams object from column definition array
                    if (a == null || a == undefined) {
                        return -1;
                    } else {
                        return 1;
                    }
                },
                formatter: function(cell) {
                    var data = cell.getRow().getData();
                    var order_id = data.order_id == null ? "-1" : data.order_id;
                    var done = data.chenh_lech.thuc_nhan == 0 && data.chenh_lech.thue == 0 && data.chenh_lech.thu_nhap == 0;
                    if (data.active == 0) {
                        var button = $("<i/>").addClass("fa fa-check-circle hover").clone();
                        button.attr("onclick", "showIgnoreModal(" + data.cross_year_id + ", true)");
                        if (ke_toan_check) {
                            button = null;
                        }
                        var layout = $("<div/>")
                            .addClass("row")
                            .append($("<div/>").addClass("col col-md-8 text-left").html("Bỏ qua do:<br>" + data.reason));
                        layout.append($("<div/>").addClass("col col-md-4 text-right").append(button));
                        data = layout.prop('outerHTML');
                        return data;
                    } else if (data.additional_order != null && data.additional_order !== undefined) {
                        var orderText = $("<span/>").addClass("year-found").html("F-" + data.additional_order.id + "(" + data.additional_order.serial + ")");
                        var button = $("<i/>").addClass("fa fa-remove hover").clone();
                        button.attr("onclick", "showRemoveModal({{$cci[0]['id']}}, " + cell.getValue().id + ")");
                        if (ke_toan_check) {
                            button = null;
                        }
                        var layout = $("<div/>")
                            .addClass("row")
                            .append($("<div/>").addClass("col col-md-6").append(orderText));
                        layout.append($("<div/>").addClass("col col-md-6 text-right").append(button));

                        return layout.prop('outerHTML');
                    } else {
                        var ignoreBtn = $("<i/>").addClass("fa fa-ban hover").clone();
                        ignoreBtn.attr("onclick", "showIgnoreModal(" + data.cross_year_id + ", false)");
                        if (ke_toan_check) {
                            ignoreBtn = null;
                        }
                        var addOrder = $("<a/>").append($("<b/>").append("+")).addClass("btn btn-primary text-light").attr('href', "/tao-bo-thanh-toan?additional_order="+order_id+"&cross_check_info_id={{$cci[0]['id']}}&cross_id="+data.cross_year_id);
                        if (done) {
                            addOrder = null;
                        }
                        var button = $("<i/>").addClass("fa fa-remove hover").clone();

                        var layout = $("<div/>")
                            .addClass("row")
                            .append($("<div/>").addClass("col col-md-6").append(addOrder));
                        layout.append($("<div/>").addClass("col col-md-6 text-right").append(ignoreBtn));

                        return layout.prop('outerHTML')
                        return "<div class='row'><div class='col col-md-6 text-center'><a href='/tao-bo-thanh-toan?additional_order="+order_id+"&cross_check_info_id={{$cci[0]['id']}}&cross_id="+data.cross_year_id+"' class='btn btn-primary text-light'>+</a></div></div>";
                    }
                },
                frozen: true,
            },
        ], ajaxRequesting: function (url, params) {
            //url - the URL of the request
            //params - the parameters passed with the request
            // $("#cross-check-year-table").tabulator("redraw", true);
            $("#loader").removeClass("fadeOut");
            $("#loader").addClass("fadeIn");
            $("#loader").css("opacity", 0.5);
        }, ajaxResponse: function (url, params, response) {
            //url - the URL of the request
            //params - the parameters passed with the request
            //response - the JSON object returned in the body of the response.
            $("#loader").addClass("fadeOut");
            $("#loader").removeClass("fadeIn");
            if (response.done_TCB) {
                $("#done-accounter").removeClass("d-none");
            } else {
                $("#done-accounter").addClass("d-none");
            }
            if (response.doneAccounter) {
                $("#done-accounter").addClass("d-none");
                ke_toan_check = true;
            }
            return response.data; //return the response data to tabulator
        }, ajaxError: function (xhr, textStatus, errorThrown) {
            //xhr - the XHR object
            //textStatus - error type
            //errorThrown - text portion of the HTTP status
            $("#loader").addClass("fadeOut");
            $("#loader").removeClass("fadeIn");
        },
        dataSorting:function(){
            // $("#cross-check-year-table").tabulator("redraw", true);
        },
        renderStarted:function(){

        },
        renderComplete:function(e){

        },
    });

    function showIgnoreModal(id, active) {
        if (active) {
            $("#confirm-set-active .modal-header").html("Tắt bỏ qua đối soát");
            $("#confirm-set-active #set-active-text").html("Bạn có muốn tắt bỏ qua đối soát cho khoản này ?");
            $(".reason").hide();
            $("#confirm-set-active-btn").attr("active" , 1);
        } else {
            $("#confirm-set-active .modal-header").html("Bỏ qua đối soát");
            $("#confirm-set-active #set-active-text").html("Bạn có muốn bỏ qua đối soát cho khoản này ?");
            $(".reason").show();
            $("#confirm-set-active-btn").attr("active" , 0);
        }
        $("#showSetActiveModal").click();
        $("#confirm-set-active-btn").attr("cross-id", id);
    }

    $("#confirm-set-active-btn").click(function () {
        var id = $("#confirm-set-active-btn").attr("cross-id");
        var active = $("#confirm-set-active-btn").attr("active");

        if (active == 0 && $("#reason").val().trim() == "") {
            makeAlert("Không thành công", "Bạn cần nhập lý do bỏ qua đối soát cho khoản này", "danger");
            $("#reason").focus();
            return;
        }

        $(".dismiss-btn").click();
        $(".dismiss-btn").click();

        setActiveCross(id, active);
    });

    function setActiveCross(id, active) {
        var params = {
            active : active,
            reason : $("#reason").val().trim(),
            cross_year : true
        };
        $.ajax({
            url: "/doi-soat/set-active/" + id,
            type: "POST",
            dataType: "json",
            beforeSend: function (request) {
                request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            },
            data: params,
            success: function (res) {
                if (res.active == 0) {
                    makeAlert("Thành công", "Bỏ qua đối soát thành công", "success");
                } else if (res.active == 1){
                    makeAlert("Thành công", "Tắt bỏ qua đối soát thành công", "success");
                } else {
                    makeAlert("Thất bại", "Có lỗi xảy ra", "danger");
                }

                $("#cross-check-year-table").tabulator("setData");
            },
            error: function (res) {
                makeAlert("Thất bại", "Có lỗi xảy ra", "danger");
            },
            complete: function () {
            }
        });
    }


    $("#confirm-delete-btn").click(function (e) {
        removeOrderId($(this).attr("data-cross-id"), $(this).attr("data-order-id"));
    });

    function showRemoveModal(crossInfoId, orderId) {
        $("#confirm-delete-btn").attr("data-order-id", orderId);
        $("#confirm-delete-btn").attr("data-cross-id", crossInfoId);
        $("#showDeleteModal").click();
    }

    function removeOrderId(crossInfoId, orderId) {
        var params = {
            crossInfoId: crossInfoId,
            orderId: orderId,
            cross_year: true,
        };
        $.ajax({
            url: "/doi-soat/remove-orderId",
            type: "POST",
            dataType: "json",
            beforeSend: function (request) {
                request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            },
            data: params,
            success: function (res) {
                if (res.result == "success") {
                    makeAlert("Thành công", "Gỡ bỏ bộ thanh toán thành công", "success");
                    $("#cross-check-year-table").tabulator("setData");
                } else {
                    makeAlert("Thất bại", "Đã có lỗi xảy ra", "danger");
                }
                $(".dismiss-btn").click();
                $(".dismiss-btn").click();
            },
            error: function (res) {
                console.log(res);
            }
        });
    }

    function khopFilter (data, filterParams) {
        var okCompare = data.chenh_lech.thuc_nhan == 0 && data.chenh_lech.thue == 0 && data.chenh_lech.thu_nhap == 0;
        var haveAdditional = data.additional_order != null;

        return (okCompare || haveAdditional) && data.active == 1; //must return a boolean, true if it passes the filter.
    }

    function lechFilter (data, filterParams) {
        var okCompare = data.chenh_lech.thuc_nhan == 0 && data.chenh_lech.thue == 0 && data.chenh_lech.thu_nhap == 0;
        var haveAdditional = data.additional_order != null;

        return (!okCompare && !haveAdditional) && data.active == 1; //must return a boolean, true if it passes the filter.
    }

    $("#filter-select").change(function (e) {
        var data = $("#filter-select").val();
        switch (data) {
            case "all" : $("#cross-check-year-table").tabulator("clearFilter"); break;
            case "khop" :
                $("#cross-check-year-table").tabulator("setFilter", khopFilter);
                break;
            case "lech" :
                $("#cross-check-year-table").tabulator("setFilter", lechFilter);
                break;
            case "bo-qua":
                $("#cross-check-year-table").tabulator("setFilter", "active", "=", 0);
                break;
            default:
                $("#cross-check-year-table").tabulator("clearFilter");
        }
    });

    $("#cancel-filter").click(function (e) {
        $("#cross-check-year-table").tabulator("clearFilter");
    });
</script>
@endSection()