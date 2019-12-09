<?php
$isDone = $isDoneTCB && $isDoneAccounter;
?>
@extends('layouts.app')

@section('custom-css')
    <style>
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

        div#cross-check-table * {
            font-size: 12px !important;
        }
    </style>
@endSection()
@section('content')
<div class="masonry-item col-md-12 w-100" id="show-cross-check">
    <div class="form-row">
        <div class="form-group col-md-6">
            <a class="btn btn-primary" href="{{route('cross_check.listCrossCheck', [
                        'pre-load-pn' => $phap_nhan,
                        'pre-load-year' => $nam
                    ])}}">Quay lại</a>
        </div>
    </div>
    <div class="bgc-white p-20 bd">
        <?php $type = $luong == "luong" ? "lương" : "ngoài lương" ?>
        <h3 class="c-grey-900">Kết quả đối soát {{$type}} tháng {{$thang}}</h3>
        <div class="mT-30" style="min-height: 600px;">
            <div class="row">
                <div class="form-group col-md-3">
                    <?=
                    Form::inputField([
                        'label' => 'Tìm kiếm qua diễn giải',
                        'name' => 'filter-value',
                        'type' => 'search',
                        'value' => '',
                        'options' => [
                            'placeholder' => 'Nhập diễn giải cần tìm kiếm',
                            'id' => 'filter-value'
                        ],
                    ]);
                    ?>
                </div>
                <div class="form-group col-md-3">
                    <label id="label-filter-value" for="filter-value"><b style="opacity: 0;">a</b></label><br>
                    <button id="search" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
                <div class="col col-md-6 text-right">
                    <div class="dropdown" style="margin-bottom: 10px" id="dd-visabale-col">
                        <label id="label-filter-value" class="d-block" style="opacity: 0" for="filter-value"><b>1</b></label>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Chọn cột hiển thị
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-end"
                             style="height: 200px;overflow-x: scroll;top:100px;width: 700px;padding: 10px">
                            <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px">Chọn trường hiển thị: <span
                                        class="btnChooseTable btnChooseTableChecked"
                                        id="btnChooseAll">Chọn tất cả</span> | <span class="btnChooseTable"
                                                                                     id="btnCustom">Tùy chỉnh</span>
                            </div>
                            <div id="fields-visible">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col col-md-12 text-right"><i>Dùng shift + cuộn chuột để cuộn ngang bảng</i></div>
                <div class="col col-md-12 text-center">
                    <div id="cross-check-table" class="tabulator table-bordered"></div>
                </div>
                <div class="col col-md-12 text-center">
                    <div id="pagination-cross-check-table" class="align-center tabulator-pagination"
                         style="display: none; margin-top: 10px"></div>
                </div>
            </div>
            <div class="form-row"style="margin-top: 10px;">
                <div class="col col-md-12">
                    <a href="/doi-soat/export/ngoai-luong/{{$phap_nhan}}/{{$thang}}/{{$nam}}" class="btn btn-primary" id="export-xlsx"><i class="fa fa-save"></i> Xuất báo cáo
                    </a>
                    @if($isDone)
                        <form action="{{route('export.o1.post')}}" method="POST" style="display: inline-block">
                            {{ csrf_field()}}
                            <input type="hidden" name="phap_nhan" value="{{$phap_nhan}}">
                            <input type="hidden" name="month" value="{{$thang}}">
                            <input type="hidden" name="year" value="{{$nam}}">
                            <button type="submit" class="btn btn-primary" id="export-o1"><i class="fa fa-save"></i>
                                Xuất File O1
                            </button>
                        </form>
                    @endif
                </div>
                <button id="showEditModal" style="display: none" data-toggle='modal'
                        data-target='#orderInfoModal'></button>
            </div>
            <div class="form-row" style="margin-top: 10px;">
                <div class="col col-md-12">
                    @if (!$isDoneTCB)
                        <a href="/tao-bo-thanh-toan?temp_order=true&cross_check_info_id={{$crossInfoId}}"
                           class="btn btn-primary"><i class="fa fa-plus"></i> Bổ sung bộ thanh toán</a>
                    @elseif(!$isDoneAccounter)
                        @if(\App\Facades\Topica::canCross("export.cross_check_kt_check", $phap_nhan))
                            <a href="/doi-soat/hoan-thanh/{{$crossInfoId}}" class="btn btn-success"><i
                                        class="fa fa-check"></i> Kế toán xác nhận hoàn thành đối soát</a>
                        @endif
                    @endif
                    @if (!$isDoneAccounter)
                        <button class="btn btn-danger" data-toggle='modal' data-target='#confirm-cancel'><i
                                    class="fa fa-remove"></i> Hủy bộ đối soát
                        </button>
                    @endif
                    @if(\App\Facades\Topica::canCross("remove.cross_check", $phap_nhan) && $isDoneAccounter)
                        <button class="btn btn-danger" data-toggle='modal' data-target='#confirm-remove'><i
                                    class="fa fa-remove"></i> Tắt hoàn thành đối soát
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal fade" id="orderInfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thông tin bộ chứng từ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="order-info-content">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
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
        <div class="modal fade" id="confirm-remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Tắt đối soát
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn tắt đối soát cho pháp nhân <b>{{$phap_nhan}}</b> tháng {{$thang}}
                            năm {{$nam}} ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="dismiss-cancel-btn" class="btn btn-default dismiss-btn" data-dismiss="modal">Hủy
                        </button>
                        <a href="/doi-soat/remove-cross-check/{{$crossInfoId}}" cross-id="{{$crossInfoId}}"
                           id="confirm-remove-btn" class="btn btn-danger">Đồng ý</a>
                    </div>
                </div>
            </div>
        </div>
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
                        <a href="/doi-soat/cancel-cross-check/{{$crossInfoId}}" cross-id="{{$crossInfoId}}"
                           id="confirm-cancel-btn" class="btn btn-danger">Đồng ý</a>
                    </div>
                </div>
            </div>
        </div>
        <button id="showDeleteModal" style="display: none" data-toggle='modal' data-target='#confirm-delete'></button>
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
    </div>
</div>
@endSection()

@section('script')
    <script>
        var ajaxParams = {
            per_page: 10,
            with_order: true,
            with_active: true
        };

        var page = 1;

        if (localStorage.getItem("current-page-{{$phap_nhan}}-{{$thang}}-{{$nam}}") !== null) {
            page = parseInt(localStorage.getItem("current-page-{{$phap_nhan}}-{{$thang}}-{{$nam}}"));
        }

        var isDone = {{$isDone ? 1 : 0}};
        var requestRender = false;
        var firstLoad = true;
        //'sumTcbReal', 'sumAccReal'
        var sumAccBeforeTax = {{$sumAccReal[0]['tong_truoc_thue'] ?? 0}};
        var sumAccTax = {{$sumAccReal[0]['tong_thue'] ?? 0}};
        var sumAccReal = {{$sumAccReal[0]['tong_thuc_tra'] ?? 0}};
        var sumTcbBeforeTax = {{$sumTcbReal[0]['tong_truoc_thue'] ?? 0}};
        var sumTcbTax = {{$sumTcbReal[0]['tong_thue'] ?? 0}};
        var sumTcbReal = {{$sumTcbReal[0]['tong_thuc_tra'] ?? 0}};
        var sumCompareBeforeTax = sumAccBeforeTax - sumTcbBeforeTax;
        var sumCompareTax = sumAccTax - sumTcbTax;
        var sumCompareReal = sumAccReal - sumTcbReal;

        $("#dropdownMenuButton").click(function (e) {
            var fields = $("#cross-check-table").tabulator("getColumns");
            $("#fields-visible").html("");
            fields.forEach(function (element) {
                if (element.getDefinition().title == "preventShow") {
                    return;
                }
                var field = $("<a/>").addClass("dropdown-item choose_col cross_choose").attr("href", "javascript:void(0)");
                var input = $("<input/>").attr("type", "checkbox").addClass("form-check-input");
                input.val(element.getDefinition().field);
                input.prop("checked", element.getVisibility() == true || element.getVisibility() == undefined ? true : false);
                var title = element.getDefinition().title;
                title = title.replace(/[\d+\,|\d+]+/g, "");
                var span = $("<span>").html(title);
                span.click(function (e) {
                    $(this).parent().click();
                });
                field.append(input);
                field.append(span);
                field.click(function (e) {
                    clickToShow(e);
                });
                $("#fields-visible").append(field);
            });
        });

        $("#filter-value").on("search", function(e) {
            var keyword = $("#filter-value").val();
            if (keyword.trim() == "") {
                var filters = $("#cross-check-table").tabulator("getFilters");
                if (filters.length != 0) {
                    $("#cross-check-table").tabulator("clearFilter");
                }
            }
        });

        $("#filter-value").keyup(function (e) {
            var code = e.keyCode || e.which;
            if (code == 13 && $("#filter-value").val().trim() != "") {
                $("#search").click();
            }

            if (code == 27) {
                var filters = $("#cross-check-table").tabulator("getFilters");
                if (filters.length != 0) {
                    $("#cross-check-table").tabulator("clearFilter");
                }
                $("#filter-value").val("");
            }
        });

        $("#search").on("click", function(e) {
            var keyword = $("#filter-value").val();
            $("#cross-check-table").tabulator("setFilter", 'dien_giai', "like", keyword);
        });

        function clickToShow(e) {
            e.stopPropagation();
            statusCheckbox = $(e.target).find('input[type=checkbox]').prop('checked');
            if (e.target.tagName != 'INPUT') {
                checkboxIsDisabled = $(e.target).find('input[type=checkbox]').prop('disabled');
                if (!checkboxIsDisabled) {
                    if (statusCheckbox) {
                        $(e.target).find('input[type=checkbox]').prop('checked', false);
                        $(e.target).find('input[type=checkbox]').data('visable', false);
                    } else {
                        $(e.target).find('input[type=checkbox]').prop('checked', true);
                        $(e.target).find('input[type=checkbox]').data('visable', true);
                    }
                }
            } else {
                $(e.target).find('input[type=checkbox]').data('visable', statusCheckbox);
            }
            renderTable();
        }

        function showPopupOrder(e) {
            e.preventDefault();
            $("#showEditModal").click();
            $("#exampleModalLabel").html("Thông tin bộ chứng từ F_" + $(e.target).attr("order_id"));
            $("#loader").removeClass("fadeOut");
            $("#loader").addClass("fadeIn");
            $("#loader").css("opacity", 0.5);
            $.ajax({
                async: true,
                url: '/bo-thanh-toan/' + $(e.target).attr("order_id"),
                type: "GET",
                success: function (msg) {
                    $("#order-info-content").html(msg);
                },
                error: function (msg) {
                    $("#order-info-content").html("Không tìm thấy thông tin bộ thanh toán");
                },
                complete: function (msg) {
                    $("#loader").addClass("fadeOut");
                    $("#loader").removeClass("fadeIn");
                }
            });
        }

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
                $("#cross-check-table").tabulator("hideColumn", v) //hide the "name" column
            })
            $.each(checkedVals, function (k, v) {
                $("#cross-check-table").tabulator("showColumn", v) //hide the "name" column
            });
            //$("#cross-check-table").tabulator("redraw", true);
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
                        {{--$("#cross-check-table").tabulator("setData", "/doi-soat/bo-doi-soat/{{$luong}}/{{$phap_nhan}}/{{$thang}}/{{$nam}}", ajaxParams);--}}
                        $("#cross-check-table").tabulator("setPage", $("#cross-check-table").tabulator("getPage"));
                        // $("#cross-check-table").tabulator("redraw", true);
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

        function openEditOrderPage(id) {
            window.open("/bo-thanh-toan/" + id+"?back="+window.location.pathname+window.location.search, "_self");
        }

        function topCalcBy(values, data, calcParams) {
            // console.log(calcParams);
            return calcParams.field;
        }

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
                reason : $("#reason").val().trim()
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

                    window.location.reload();
                },
                error: function (res) {
                    makeAlert("Thất bại", "Có lỗi xảy ra", "danger");
                },
                complete: function () {
                }
            });
        }

        $(document).ready(function () {
            $('#cross-check-table .tabulator-tableHolder').scroll(function () {
                scrollLeft = $('#cross-check-table .tabulator-tableHolder').scrollLeft();
                if (scrollLeft !== 0) {
                    localStorage.setItem('cross-check-table-scroll', scrollLeft);
                }
            });
        });

        $("#cross-check-table").tabulator({
            ajaxURL: "/doi-soat/bo-doi-soat/{{$luong}}/{{$phap_nhan}}/{{$thang}}/{{$nam}}", //ajax URL
            ajaxParams: ajaxParams, //ajax parameters
            ajaxFiltering:true,
            ajaxConfig: {
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }, //ajax HTTP request type
            height: "100%", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
            layout: "fitData", //fit columns to width of table (optional)
            autoResize: true,
            persistenceID:"cross-check-table",
            persistenceMode:"cookie", //set persistent storage mode
            // persistentLayout:true, //enable persistent column layout
            persistentSort:true,
            layoutColumnsOnNewData: true,
            pagination: "remote",
            widthShrink: [''],
            ajaxSorting: true,
            paginationDataReceived: {
                "max_pages": "last_page"
            },
            ajaxLoader: false,
            columnVertAlign: "bottom",
            // ajaxLoaderLoading: '<div class="loader"></div>',
            paginationElement: $("#pagination-cross-check-table"),
            columns: [ //Define Table Columns
                {
                    title: "#", headerSort: false, field: "idx", formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the column
                        var page = $("#cross-check-table").tabulator("getPage") - 1;
                        var size = ajaxParams['per_page'];
                        return (page * size) + cell.getRow().getPosition(true) + 1;
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
                    visible: false,
                    formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the column

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
                        var data = JSON.parse(cell.getValue());

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
                    title: "Kế toán",
                    columns: [
                        {
                            title: "Thu nhập <br>" + currencyFormat(sumAccBeforeTax),
                            visible: true,
                            cssClass: "accounter_column",
                            headerSort: false,
                            formatter: function (cell, formatterParams) {
                                var data = cell.getRow().getData();
                                var money = currencyFormat(parseInt(data.ps_no) + parseInt(data.thue));
                                return money;
                            },
                            // formatter: "money",
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thuế <br>" + currencyFormat(sumAccTax),
                            cssClass: "accounter_column",
                            field: "thue",
                            visible: true,
                            formatter: "money",
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thực nhận <br>" + currencyFormat(sumAccReal),
                            cssClass: "accounter_column",
                            field: "ps_no",
                            visible: true,
                            formatter: "money",
                            formatterParams: {precision: 0}
                        },
                    ]
                },
                {
                    title: "TCB",
                    columns: [
                        {
                            title: "Thu nhập <br>"+ currencyFormat(sumTcbBeforeTax),
                            cssClass: "TCB_column",
                            visible: true,
                            headerSort: false,
                            mutator:function(value, data, type, params, component){
                                if (data.order_id != null && data.temp_order == 0) {
                                    return data.tnct;
                                } else {
                                    if (data.order == null || data.order.quy_doi == null) {
                                        return null;
                                    }
                                    var money = data.tnct;
                                    return money;
                                }
                            },
                            formatter: "money",
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thuế <br>" + currencyFormat(sumTcbTax),
                            cssClass: "TCB_column",
                            field: "tax",
                            headerSort: false,
                            visible: true,
                            mutator:function(value, data, type, params, component){
                                if (data.order_id != null && data.temp_order == 0) {
                                    return data.thue;
                                } else {
                                    return data.tax;
                                }
                            },
                            formatter: "money",
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thực nhận <br>" + currencyFormat(sumTcbReal),
                            cssClass: "TCB_column",
                            field: "order.quy_doi",
                            headerSort: false,
                            visible: true,
                            mutator:function(value, data, type, params, component){
                                if (data.order_id != null && data.temp_order == 0) {
                                    return data.ps_no;
                                } else {
                                    if (data.order != null) {
                                        return data.order.quy_doi;
                                    } else {
                                        return "";
                                    }
                                }
                            },
                            formatter: "money",
                            formatterParams: {precision: 0}
                        },
                    ]
                },
                {
                    title: "Chênh lệch",
                    columns: [
                        {
                            title: "Thu nhập <br>" + currencyFormat(sumCompareBeforeTax),
                            cssClass: "compare_column",
                            visible: true,
                            headerSort: false,
                            formatter: function (cell, formatterParams) {

                                var data = cell.getRow().getData();
                                if (data.active == 0) return "";
                                if (data.order_id == null) {
                                    return "";
                                }

                                var money = currencyFormat((parseInt(data.ps_no) + parseInt(data.thue)) - (parseInt(data.tnct)));
                                return money;
                            },
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thuế <br>" + currencyFormat(sumCompareTax),
                            cssClass: "compare_column",
                            field: "tax",
                            headerSort: false,
                            visible: true,
                            formatter: function (cell, formatterParams) {

                                var data = cell.getRow().getData();
                                if (data.active == 0) return "";
                                if (data.order_id == null) {
                                    return "";
                                }

                                var money = currencyFormat(data.thue - data.tax);
                                return money;
                            },
                            formatterParams: {precision: 0}
                        },
                        {
                            title: "Thực nhận <br>" + currencyFormat(sumCompareReal),
                            cssClass: "compare_column",
                            field: "order.quy_doi",
                            headerSort: false,
                            visible: true,
                            formatter: function (cell, formatterParams) {

                                var data = cell.getRow().getData();
                                if (data.active == 0) return "";
                                if (data.order_id == null) {
                                    return "";
                                }
                                var money = currencyFormat(parseInt(data.ps_no) - parseInt(data.order.quy_doi) );
                                return money;
                            },
                            formatterParams: {precision: 0, symbolAfter: false}
                        },
                    ]
                },
                {
                    title: "Bộ thanh toán",
                    field: "order_id",
                    frozen: true,
                    formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the
                        var data = "Không thấy";
                        var rowData = cell.getRow().getData();
                        var tempOrder = rowData.temp_order;
                        var serial = rowData.order != null && rowData.order.serial != "" ? " ("+rowData.order.serial+")" : "";
                        if (rowData.active == 0) {
                            var button = $("<i/>").addClass("fa fa-check-circle hover").clone();
                            button.attr("onclick", "showIgnoreModal(" + rowData.id + ", true)");
                            var layout = $("<div/>")
                                .addClass("row")
                                .append($("<div/>").addClass("col col-md-8 text-left").html("Bỏ qua do:<br>" + rowData.reason));
                            if (isDone !== 1) {
                                layout.append($("<div/>").addClass("col col-md-4 text-right").append(button));
                            }
                            data = layout.prop('outerHTML');
                            return data;
                        }
                        if (cell.getValue() !== null) {
                            var link = $("<a/>").html("F-" + cell.getValue() + serial).clone();
                            link.attr("order_id", cell.getValue());
                            link.addClass("popup-order-detail");
                            link.prop("href", "/bo-thanh-toan/" + cell.getValue());
                            link.attr("onclick", "showPopupOrder(event)");
                            var button = $("<i/>").addClass(tempOrder == 1 ? "fa fa-pencil hover" : "fa fa-remove hover").clone();
                            if (tempOrder == 1) {
                                link.css("color", "#f0ad4e");
                                button.attr("onclick", "openEditOrderPage("+cell.getValue()+")");
                            } else {
                                button.attr("onclick", "showRemoveModal({{$crossInfoId}}, " + cell.getValue() + ")");
                            }
                            var layout = $("<div/>")
                                .addClass("row")
                                .append($("<div/>").addClass("col col-md-6").append(link));
                            if (isDone !== 1) {
                                layout.append($("<div/>").addClass("col col-md-6 text-right").append(button));
                                if (tempOrder == 1) {
                                    var ignoreButton = $("<i/>").addClass("fa fa-ban hover").clone();
                                    ignoreButton.attr("onclick", "showIgnoreModal(" + rowData.id + ", false)");
                                    layout.append($("<div/>").addClass("col col-md-12 text-right").append(ignoreButton));
                                }
                            }
                            if (tempOrder == 1 && rowData.order.phap_nhan != "{{$phap_nhan}}") {
                                link.append("<br/>Khác pháp nhân");
                            }
                            data = layout.prop('outerHTML');
                        } else {
                            var layout = $("<div/>")
                                .addClass("row")
                                .append($("<div/>").addClass("col col-md-6").append("Không thấy"));
                            var ignoreButton = $("<i/>").addClass("fa fa-ban hover").clone();
                            ignoreButton.attr("onclick", "showIgnoreModal(" + rowData.id + ", false)");
                            layout.append($("<div/>").addClass("col col-md-6 text-right").append(ignoreButton));

                            data = layout.prop('outerHTML');
                        }

                        return data;
                    }
                },
            ],
            cellEdited: function (cell) { //trigger an alert message when the row is clicked

            }, cellEditCancelled: function (cell) {

            },
            rowClick: function (e, row) {

            },
            rowContext: function (e, row) {
                //e - the click event object
                //row - row component

                e.preventDefault(); // prevent the browsers default context menu form appearing.
            }, dataEdited: function (data) {

            }, ajaxRequesting: function (url, params) {
                //url - the URL of the request
                //params - the parameters passed with the request
                // $("#cross-check-table").tabulator("redraw", true);
                $("#loader").removeClass("fadeOut");
                $("#loader").addClass("fadeIn");
                $("#loader").css("opacity", 0.5);
            }, ajaxResponse: function (url, params, response) {
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
                response.data.forEach(function (element) {
                    if (element['tax'].length > 0) {
                        if (element['order_id'] == null) {
                            element['tax'] = null;
                        } else {
                            element['tax'] = element['tax'][0]['sumTax'];
                            element.thuc_nhan = element['order']['quy_doi'] - element['tax'];
                        }
                    }

                    if (element['tnct'].length > 0) {
                        if (element['order_id'] == null) {
                            element['tnct'] = null;
                        } else {
                            element['tnct'] = element['tnct'][0]['sumTnct'];
                        }
                    }
                });
                if (response['last_page'] == 1) {
                    $("#pagination-cross-check-table").hide();
                } else {
                    $("#pagination-cross-check-table").show();
                }
                localStorage.setItem("current-page-{{$phap_nhan}}-{{$thang}}-{{$nam}}", $("#cross-check-table").tabulator("getPage"));
                requestRender = true;
                return response; //return the response data to tabulator
            }, ajaxError: function (xhr, textStatus, errorThrown) {
                //xhr - the XHR object
                //textStatus - error type
                //errorThrown - text portion of the HTTP status
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
            },
            dataSorting:function(){
                // $("#cross-check-table").tabulator("redraw", true);
            },
            renderStarted:function(){

            },
            renderComplete:function(e){
                if (requestRender) {
                    if (firstLoad) {
                        $("#cross-check-table").tabulator("setPage", page);
                        firstLoad = false;
                        return;
                    }
                    setTimeout(function () {
                        $("#cross-check-table").tabulator("scrollToColumn", "idx", "left", false);

                        if (localStorage.getItem('cross-check-table-scroll') !== null) {
                            $("#cross-check-table .tabulator-tableHolder").animate({scrollLeft: localStorage.getItem('cross-check-table-scroll')}, 200);
                        }

                        requestRender = false;
                    }, 200);
                }
            },
            locale: true,
            langs: {
                "vi": {
                    "pagination": {
                        "first": "First", //text for the first page button
                        "first_title": "First Page", //tooltip text for the first page button
                        "last": "Last",
                        "last_title": "Last Page",
                        "prev": "&larr;",
                        "prev_title": "Prev Page",
                        "next": "&rarr;",
                        "next_title": "Next Page",
                    }
                }
            },
        });
    </script>
@endSection()