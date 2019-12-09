@extends('layouts.app')
@section('custom-css')
<style>
    .fail-pick {
        color: red;
    }

    .success-pick {
        color: green;
    }
</style>
@endSection
@section('content')
    <div class="masonry-item col-md-12 w-100" id="show-cross-check">
        <div class="bgc-white p-20 bd">
            <h3 class="c-grey-900">Đối soát bộ chứng từ F_{{$order['id']}} @if($cross_check_month != null) cho tháng {{$cross_check_month}} @endIf()</h3>
            <div class="row">
                <div class="col-md-3"><p>Nội dung bộ chứng từ :</p></div>
                <div class="col-md-8"><p class="dien_giai">{{$order['noi_dung']}}</p></div>
            </div>
            <div class="row">
                <div class="col-md-3"><p>Tổng tiền bộ F-{{$order['id']}} :</p></div>
                <div class="col-md-3"><p>{{number_format($order['quy_doi'])}} VNĐ</p></div>
                <div class="col-md-3"><p>Tổng thuế bộ F-{{$order['id']}} :</p></div>
                <div class="col-md-3"><p>{{number_format($order['thue'])}} VNĐ</p></div>
            </div>
            <div class="row">
                <div class="col-md-3"><p>Tổng tiền các ô được chọn :</p></div>
                <div class="col-md-3"><p class="orders-money fail-pick">0 VNĐ</p></div>
                <div class="col-md-3"><p>Tổng tiền thuế các ô được chọn :</p></div>
                <div class="col-md-3"><p class="orders-tax fail-pick">0 VNĐ</p></div>
            </div>
            <input id="quy_doi" type="hidden" value="{{$order['quy_doi']}}">
            <input id="thue" type="hidden" value="{{$order['thue']}}">
            <div class="row">
                <div class="form-group col-md-3">
                    <?=
                    Form::inputField([
                        'label' => 'Tìm kiếm qua diễn giải',
                        'name' => 'filter-value',
                        'type' => 'text',
                        'value' => '',
                        'options' => [
                            'placeholder' => 'Nhập giá trị cần tìm kiếm',
                            'id' => 'filter-value'
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-6">
                    <div class="dropdown pull-left" style="margin-bottom: 10px" id="dd-visabale-col">
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
                            <div id="fields-visible">
                                <a class="dropdown-item choose_col cross_choose"
                                   style="width:48%;display: inline-block;word-break: break-all;white-space: normal;"
                                   href="javascript:void(0)">
                                    <input class="form-check-input" data-visable=""
                                           value="aaa" type="checkbox" >
                                    <span>hahaha</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-md-6 text-right"><i>Dùng shift + cuộn chuột để cuộn ngang bảng</i></div>
                <div class="col col-md-12"><div id="pick-orders-table" class="tabulator table-bordered"></div></div>

                <div id="pagination-pick-orders-table" class="align-center tabulator-pagination"
                     style="display: none; margin-top: 20px"></div>
            </div>
            <div class="row">
                <div class="col col-md-12">
                    <button id="save-cross-check" class="btn btn-primary">Lưu kết quả</button>
                    <button id="cancel-cross-check" class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete">Hủy kết quả</button>
                </div>
            </div>

            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            Hủy đối soát
                        </div>
                        <div class="modal-body">
                            <p>Mọi dữ liệu về bộ thanh toán sẽ bị hủy khi bạn thực hiện hành vi này !</p>
                            <p>Bạn có chắc chắn muốn hủy bộ đối soát ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                            <a href="{{route("order.deleteOrder", [
                                "cross_check_info_id" => $cross_check_info_id,
                                "order_id" => $order['id'],
                                "month" => $cross_check_month,
                                "year" => $cross_check_year,
                                "pn" => $cross_check_pn,
                                "luong" => $cross_check_luong,
                            ])}}" id="confirm-delete" class="btn btn-danger">Đồng ý</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endSection()
@section('script')
    <script>
        var allowPick = true;
        var sum = 0;
        var sumTax = 0;
        var ajaxParams = {
            @if($cross_check_month != null)
            filter: [
                {
                    "field": "ps_no",
                    "operator": "<=",
                    "value": $("#quy_doi").val()
                },
                {
                    "field": "thue",
                    "operator": "<=",
                    "value": $("#thue").val()
                }
            ],
            @endIf()
            sort: [
                {
                    "field": "serial",
                    "direction": "ASC"
                }
            ],
            @if($cross_check_month == null)
            with_month_order: true,
            @endIf()
            suggest: $(".dien_giai").html(),
            per_page: 15,
            temp_order: 1,
            pagination: false
        };

        $("#dropdownMenuButton").click(function (e) {
            console.log($("#pick-orders-table").tabulator("getColumns"));
            var fields = $("#pick-orders-table").tabulator("getColumns");
            $("#fields-visible").html("");
            fields.forEach(function(element) {
                if (element.getDefinition().title == "preventShow") {
                    return;
                }
                var field = $("<a/>").addClass("dropdown-item choose_col cross_choose").attr("href", "javascript:void(0)");
                var input = $("<input/>").attr("type", "checkbox").addClass("form-check-input");
                input.val(element.getDefinition().field);
                input.prop("checked", element.getVisibility() == true || element.getVisibility() == undefined ? true : false);
                var span = $("<span>").html(element.getDefinition().title);
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
            $("#exampleModalLabel").html("Thông tin bộ chứng từ F_"+$(e.target).attr("order_id"));
            $("#loader").removeClass("fadeOut");
            $("#loader").addClass("fadeIn");
            $("#loader").css("opacity", 0.5);
            $.ajax({
                async: true,
                url: '/bo-thanh-toan/'+$(e.target).attr("order_id"),
                type: "GET",
                success: function (msg) {
                    $("#order-info-content").html(msg);
                },
                complete: function(msg) {
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
                $("#pick-orders-table").tabulator("hideColumn", v) //hide the "name" column
            })
            $.each(checkedVals, function (k, v) {
                $("#pick-orders-table").tabulator("showColumn", v) //hide the "name" column
            });
            $("#pick-orders-table").tabulator("redraw", true);
        }

        $("#filter-value").on("keyup", function(e) {
            var keyword = $(this).val();
            $("#pick-orders-table").tabulator("setFilter", 'dien_giai', "like", keyword);
        });

        $("#pick-orders-table").tabulator({
            ajaxURL: "/doi-soat/bo-doi-soat/{{$cross_check_luong}}/{{$cross_check_pn}}/{{$cross_check_month == null ? "null" : $cross_check_month}}/{{$cross_check_year}}", //ajax URL
            ajaxParams: ajaxParams, //ajax parameters
            ajaxConfig: {
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }, //ajax HTTP request type
            height: "100%", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
            layout: "fitData", //fit columns to width of table (optional)
            autoResize: true,
            layoutColumnsOnNewData: true,
            widthShrink: [''],
            pagination: "local", //enable local pagination.
            paginationSize: 30, // this option can take any positive integer value (default = 10)
            paginationElement: $("#pagination-pick-orders-table"),
            ajaxLoader: false,
            // ajaxLoaderLoading: '<div class="loader"></div>',
            columns: [ //Define Table Columns
                {
                    title: "#", headerSort:false, field: "position", formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the column

                        return cell.getRow().getPosition(true);
                    }
                },
                {title: "Mã khách", field: "ma_khach", visible: false, width: 110},
                {title: "Tên khách", field: "ten_khach", visible: false},
                {
                    title: "Ngày chứng từ",
                    field: "ngay_chung_tu",
                    visible: true,
                    formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the column

                        return dateFormatter(cell.getValue());
                    },
                    width: 140
                },
                {title: "Mã CT", field: "ma_chung_tu", width: 90, visible: false},
                {title: "preventShow", field: "id", visible: false},
                {title: "Số chứng từ", field: "so_chung_tu", visible: false},
                {
                    title: "Diễn giải", field: "dien_giai", formatter: function (cell, formatterParams) {
                        //cell - the cell component
                        //formatterParams - parameters set for the
                        var data = JSON.parse(cell.getValue());

                        return data.join("<br>");
                    }
                },
                {
                    title: "PS nợ",
                    field: "ps_no",
                    visible: true,
                    formatter: "money",
                    formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
                },
                {
                    title: "Thuế",
                    field: "thue",
                    visible: true,
                    formatter: "money",
                    formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
                },
                {title: "Tài khoản Đ.Ứ", field: "tai_khoan_doi_ung"},
                {
                    title: "Bộ thanh toán", field: "pick", frozen: true, formatter: function (cell) {
                        var input = $("<input/>").prop("type", "checkbox").prop("disabled", true);
                        input.attr('checked', cell.getValue());
                        input.click(function (e) {
                            e.preventDefault();
                        });
                        return input.prop('outerHTML');
                    }
                },

            ],
            cellEdited: function (cell) { //trigger an alert message when the row is clicked

            }, cellEditCancelled: function (cell) {

            },
            cellClick: function (e, cell) {
                if (cell.getColumn().getDefinition().field == "pick") {
                    if (!cell.getValue() && !allowPick) {
                        makeAlert("Cảnh báo", "Số tiền hoặc thuế đối soát lớn hơn số tiền hoặc thuế bộ thanh toán", "danger");
                        return;
                    }

                    var compare = parseInt(cell.getRow().getCell("ps_no").getValue()) + sum > $("#quy_doi").val() || parseInt(cell.getRow().getCell("thue").getValue()) + sumTax > $("#thue").val();

                    if (!cell.getValue() && compare) {
                        makeAlert("Cảnh báo", "Số tiền hoặc thuế đối soát lớn hơn số tiền hoặc thuế bộ thanh toán", "danger");
                        return;
                    }
                    cell.setValue(!cell.getValue());
                    var data = $("#pick-orders-table").tabulator("getData");
                    sum = 0;
                    sumTax = 0;

                    data.forEach(function (element) {
                        if (element['pick'] == true) {
                            sum += parseInt(element['ps_no']);
                            sumTax += parseInt(element['thue']);
                        }
                    });
                    allowPick = sum < $("#quy_doi").val() || sumTax < $("#thue").val();

                    if (sum == $("#quy_doi").val()) {
                        $(".orders-money").removeClass("fail-pick");
                        $(".orders-money").addClass("success-pick");
                    } else {
                        $(".orders-money").addClass("fail-pick");
                        $(".orders-money").removeClass("success-pick");
                    }

                    if (sumTax == $("#thue").val()) {
                        $(".orders-tax").removeClass("fail-pick");
                        $(".orders-tax").addClass("success-pick");
                    } else {
                        $(".orders-tax").addClass("fail-pick");
                        $(".orders-tax").removeClass("success-pick");
                    }

                    $(".orders-money").html(currencyFormat(sum) + " VNĐ");
                    $(".orders-tax").html(currencyFormat(sumTax) + " VNĐ");
                }
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
                $("#loader").removeClass("fadeOut");
                $("#loader").addClass("fadeIn");
                $("#loader").css("opacity", 0.5);
            }, ajaxResponse: function (url, params, response) {
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
                // console.log(response);
                response.forEach(function (element) {
                    if (element['tax'].length > 0) {
                        if (element['order_id'] == null) {
                            element['tax'] = null;
                        } else {
                            element['tax'] = element['tax'][0]['sumTax'];
                            element.thuc_nhan = element['order']['quy_doi'] - element['tax'];
                        }
                    }
                    element.pick = false;
                });
                if (response['last_page'] == 1) {
                    $("#pagination-pick-orders-table").hide();
                } else {
                    $("#pagination-pick-orders-table").show();
                }
                return response; //return the response data to tabulator
            }, ajaxError: function (xhr, textStatus, errorThrown) {
                //xhr - the XHR object
                //textStatus - error type
                //errorThrown - text portion of the HTTP status
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
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

        $("#save-cross-check").click(function (e) {
            var data = $("#pick-orders-table").tabulator("getData");
            var pickList = new Array();
            var params = {};
            if (sum != $("#quy_doi").val() || sumTax != $("#thue").val()) {
                makeAlert("Không thành công", "Số tiền hoặc thuế bộ F không khớp với tổng số tiền hoặc thuế các đối soát được chọn", "danger");
                return;
            }
            data.forEach(element => {
                if (element['pick'] == true) {
                    pickList.push(element);
                }
            });

            params.data = pickList;
            params.order_id = <?= $order['id'] ?>;
            params.cross_check_month = <?= $cross_check_month == null ? "null" : $cross_check_month ?>;
            params.cross_check_year = <?= $cross_check_year ?>;
            params.cross_check_pn = "<?= $cross_check_pn ?>";
            params.cross_check_luong = "<?= $cross_check_luong ?>";
            var that = this;
            $(that).prop("disabled", true);
            $.ajax({
                url: "/doi-soat/thu-cong",
                type: "POST",
                dataType: "json",
                beforeSend: function (request) {
                    request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
                },
                data: params,
                success: function (res) {
                    if (res.result == "success") {
                        localStorage['web_message'] = JSON.stringify({
                            title: "Thành công",
                            content: "Đối soát thủ công thành công",
                            type: "success"
                        });
                        window.location.replace(res.redirect_url);
                    }
                },
                error: function (res) {
                    console.log(res);
                },
                complete: function (res) {
                    $(that).prop("disabled", false);
                }
            });
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
    </script>
@endSection()