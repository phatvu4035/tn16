<?php
$isDone = false;
?>
@extends('layouts.app')
@section('content')
    <button class="btn btn-primary" id="export-xlsx"><i class="fa fa-save"></i> Xuất báo cáo</button>
    <div id="cross-check-table" class="tabulator table-bordered"></div>
@endSection()

@section('script')
    <script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>

    <script>

    var ajaxParams = {
        pagination : false
    };
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
        height: "", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
        layout: "fitData", //fit columns to width of table (optional)
        autoResize: true,
        layoutColumnsOnNewData: true,
        ajaxLoader: false,
        columnVertAlign: "bottom",
        columns: [ //Define Table Columns
            {
                title: "Ngày chứng từ",
                field: "ngay_chung_tu",
                formatter: function (cell, formatterParams) {
                    //cell - the cell component
                    //formatterParams - parameters set for the column

                    return dateFormatter(cell.getValue());
                },
                accessorDownload: function(value, data, type, params, column){
                    return dateFormatter(value);
                },
                width: 140
            },
            {title: "Mã CT", field: "ma_chung_tu", width: 90},
            {title: "Số chứng từ", field: "so_chung_tu"},
            {
                title: "Diễn giải", field: "dien_giai", formatter: function (cell, formatterParams) {
                    //cell - the cell component
                    //formatterParams - parameters set for the
                    var data = JSON.parse(cell.getValue());

                    return data.join("<br>");
                },
                accessorDownload: function(value, data, type, params, column){
                    var data = JSON.parse(value);

                    return data.join("\n");
                },
            },
            {title: "Tài khoản Đ.Ứ", field: "tai_khoan_doi_ung"},
            {
                title: "Kế toán",
                columns: [
                    {
                        title: "Thu nhập Kế Toán",
                        visible: true,
                        field: "thu_nhap_ke_toan",
                        cssClass: "accounter_column",
                        headerSort: false,
                        formatter: function (cell, formatterParams) {
                            var data = cell.getRow().getData();
                            var money = (data.ps_no + data.thue);
                            return money;
                        },
                        accessorDownload: function(value, data, type, params, column){
                            var money = (data.ps_no + data.thue);
                            return money;
                        },
                    },
                    {
                        title: "Thuế Kế toán",
                        cssClass: "accounter_column",
                        field: "thue",
                        visible: true
                    },
                    {
                        title: "Thực nhận Kế toán",
                        cssClass: "accounter_column",
                        field: "ps_no",
                        visible: true
                    },
                ]
            },
            {
                title: "TCB",
                columns: [
                    {
                        title: "Thu nhập TCB",
                        cssClass: "TCB_column",
                        field: "thu_nhap_tcb",
                        visible: true,
                        headerSort: false,
                        formatter: function (cell, formatterParams) {
                            var data = cell.getRow().getData();
                            if (data.order == null || data.order.quy_doi == null) {
                                return null;
                            }
                            var money = (parseInt(data.order.quy_doi) + parseInt(data.tax));
                            return money;
                        },
                        accessorDownload: function(value, data, type, params, column){
                            if (data.order == null || data.order.quy_doi == null) {
                                return null;
                            }
                            var money = (parseInt(data.order.quy_doi) + parseInt(data.tax));
                            return money;
                        },
                    },
                    {
                        title: "Thuế TCB",
                        cssClass: "TCB_column",
                        field: "tax",
                        headerSort: false,
                        visible: true
                    },
                    {
                        title: "Thực nhận TCB",
                        cssClass: "TCB_column",
                        field: "order.quy_doi",
                        headerSort: false,
                        visible: true
                    },
                ]
            },
            {
                title: "Chênh lệch",
                columns: [
                    {
                        title: "Thu nhập",
                        cssClass: "compare_column",
                        field: "thu_nhap_chenh_lech",
                        visible: true,
                        headerSort: false,
                        formatter: function (cell, formatterParams) {

                            var data = cell.getRow().getData();
                            if (data.order.quy_doi == null) {
                                return null;
                            }

                            var money = ((parseInt(data.ps_no) + parseInt(data.thue)) - (parseInt(data.order.quy_doi) + parseInt(data.tax)));
                            return money;
                        },
                        accessorDownload: function(value, data, type, params, column){
                            if (data.order.quy_doi == null) {
                                return null;
                            }

                            var money = ((parseInt(data.ps_no) + parseInt(data.thue)) - (parseInt(data.order.quy_doi) + parseInt(data.tax)));
                            return money;
                        },
                    },
                    {
                        title: "Thuế",
                        cssClass: "compare_column",
                        field: "thue_chenh_lech",
                        headerSort: false,
                        visible: true,
                        formatter: function (cell, formatterParams) {

                            var data = cell.getRow().getData();
                            if (data.order.quy_doi == null) {
                                return null;
                            }

                            var money = (data.thue - data.tax);
                            return money;
                        },
                        accessorDownload: function(value, data, type, params, column){
                            if (data.order.quy_doi == null) {
                                return null;
                            }

                            var money = (data.thue - data.tax);
                            return money;
                        },
                    },
                    {
                        title: "Thực nhận",
                        cssClass: "compare_column",
                        field: "thuc_nhan_chenh_lech",
                        headerSort: false,
                        visible: true,
                        formatter: function (cell, formatterParams) {

                            var data = cell.getRow().getData();

                            if (data.order.quy_doi == null) {
                                return null;
                            }
                            var money = (parseInt(data.ps_no) - parseInt(data.order.quy_doi) );
                            return money;
                        },
                        accessorDownload: function(value, data, type, params, column){
                            if (data.order.quy_doi == null) {
                                return null;
                            }
                            var money = (parseInt(data.ps_no) - parseInt(data.order.quy_doi) );
                            return money;
                        },
                    },
                ]
            },
            {
                title: "Bộ thanh toán",
                field: "order_id",
                frozen: true,
            },
        ], ajaxRequesting: function (url, params) {
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
            response.forEach(function (element) {
                if (element['tax'].length > 0) {
                    if (element['order_id'] == null) {
                        element['tax'] = null;
                    } else {
                        element['tax'] = element['tax'][0]['sumTax'];
                        element.thuc_nhan = element['order']['quy_doi'] - element['tax'];
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
        }
    });
    $("#export-xlsx").click(function () {
        $("#cross-check-table").tabulator("download", "xlsx", "data.xlsx", {sheetName:"MyData"});
    });
</script>
@endSection()