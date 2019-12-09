@extends('layouts.app')

@section('content')
<div class="masonry-item col-md-12 w-100" id="show-cross-check">
    <div class="bgc-white p-20 bd">
        <h3 class="c-grey-900">Đối soát sổ kế toán</h3>
        <div class="mT-30">
            <div class="row">
                <div class="col col-md-3">Chọn pháp nhân</div>
                <div class="col col-md-4">
                    {{
                        Form::dropDown([
                            'label' => '',
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
            </div>
            <div class="row">
                <div class="col col-md-3">Chọn năm</div>
                <div class="col col-md-2">
                    {{
                        Form::dropDown([
                            'label' => null,
                            'name' => 'nam',
                            'data' => $listYear,
                            'selected' => intval(date("Y")),
                            'options' => [
                                'id' => 'nam',
                                'class' => 'nam list-cross-dropdown',
                                'required' => true
                            ],
                        ])
                    }}
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12">
                    <div id="cross-info-table" class="tabulator table-bordered"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endSection()
@section("script")
<script>
    $(document).ready(function (e) {
        var url = window.location.href;
        var pn = getParameterByName("pre-load-pn", url);
        var nam = getParameterByName("pre-load-year", url);

        if (pn != null && nam != null) {
            $("#phap_nhan").empty().append('<option value="'+pn+'">'+pn+'</option>').val(pn).trigger('change');
            $('#nam').val(nam).trigger('change');
        }
    });

    $("#phap_nhan").select2({
        language: "vi",
        placeholder: 'Chọn pháp nhân',
        ajax: {
            url: '/dm4c/pt',
            data: function (term, page) {
            return {
                q: term, // search term
                checkPermission: true, //Get your value from other elements using Query, for example.
                page_limit: 10
            };},
            dataType: 'json'
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        }
    });

    $('#phap_nhan').on('select2:select', function (e) {

    });

    function getListCross(year, pn) {
        $.ajax({
            async: true,
            url: '/doi-soat/danh-sach',
            type: "POST",
            data: {
                "nam" : year,
                "phap_nhan" : pn
            },
            beforeSend : function (e) {
                e.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            },
            success: function (msg) {
                console.log(msg);
            },
            complete: function(msg) {
                console.log(msg);
            }
        });
    }

    var ajaxParams = {
        "nam" : null,
        "phap_nhan" : null
    };

    $("#phap_nhan, #nam").change(function(e) {
        var done = true;
        $.each($(".list-cross-dropdown"), function (i, l) {
            if ($(l).val() == 0)
                done = false;
        });

        if (done) {
            ajaxParams['nam'] = $("#nam").val();
            ajaxParams['phap_nhan'] = $("#phap_nhan").val();
            var url = [location.protocol, '//', location.host, location.pathname].join('');
            history.pushState('data', '', url + "?pre-load-pn=" + ajaxParams['phap_nhan'] + "&pre-load-year=" + ajaxParams['nam']);
            $("#cross-info-table").tabulator("setData", "/doi-soat/danh-sach", ajaxParams);
        }
    });

    $("#cross-info-table").tabulator({
        // ajaxURL:"/doi-soat/danh-sach", //ajax URL
        ajaxParams: ajaxParams, //ajax parameters
        ajaxConfig: {
            type : "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }, //ajax HTTP request type
        height: "", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
        layout:"fitColumns", //fit columns to width of table (optional)
        autoResize: true,
        layoutColumnsOnNewData:true,
        ajaxLoader: false,
        columnVertAlign:"bottom",
        groupBy: "thang",
        groupToggleElement: false,
        groupHeader:function(value, count, data, group){
            //value - the value all members of this group share
            //count - the number of rows in this group
            //data - an array of all the row data objects in this group
            //group - the group component for the group

            return "Tháng " + value;
        },
        // ajaxLoaderLoading: '<div class="loader"></div>',
        columns:[ //Define Table Columns
            {title:"preventShow", field:"id", visible: false},
            {title:"preventShow", field:"nam", visible: false},
            {title:"Loại", field:"is_salary", width: 200},
            {title:"Tháng", field:"thang", visible: false, width: 200},
            {title:"Trạng thái TCB", field:"statusTCB", visible: true, formatter : function (cell, param){
                var value = cell.getValue();

                switch (value) {
                    case "Đang lưu tạm" :
                        cell.getRow().getElement().css({
                            "background-color": "#ffffba"
                        });
                        break;
                    case "Chưa đối soát" :
                        cell.getRow().getElement().css({
                            "background-color": "#ffb3ba"
                        });
                        break;
                    case "Đã đối soát" :
                        if (cell.getRow().row.data.statusAccounter == "Đã đối soát") {
                            cell.getRow().getElement().css({
                                "background-color": "#baffc9"
                            });
                        } else {
                            cell.getRow().getElement().css({
                                "background-color": "#ffffba"
                            });
                        }
                        break;
                }

                return value;
            }},
            {title:"Trạng thái kế toán", field:"statusAccounter", visible: true},

        ],
        rowClick: function (e, row) {
            if (row.getCell("is_salary").getValue() == "Ngoài lương") {
                if(row.getCell("statusTCB").getValue() == "Chưa đối soát") {
                    window.open("/doi-soat/import/ngoai-luong/"+ajaxParams['phap_nhan']+"/"+row.getCell("thang").getValue()+"/"+row.getCell("nam").getValue(), "_self");
                } else {
                    window.open("/doi-soat/bo-doi-soat/ngoai-luong/"+ajaxParams['phap_nhan']+"/"+row.getCell("thang").getValue()+"/"+row.getCell("nam").getValue(), "_self");
                }
            } else if (row.getCell("is_salary").getValue() == "Lương") {
                var data = $("#cross-info-table").tabulator("getData");
                var foundRow = undefined;
                data.forEach(function (item, index) {
                    if (item.is_salary == "Ngoài lương" && item.thang == row.getCell("thang").getValue()) {
                        foundRow = item;
                        return false;
                    }
                });

                if (foundRow.statusTCB == "Chưa đối soát" || foundRow.statusAccounter == "Chưa đối soát") {
                    makeAlert("Thất bại", "Bạn cần hoàn thành đối soát ngoài lương trước khi đối soát lương", "danger");
                } else {
                    if(row.getCell("statusTCB").getValue() == "Chưa đối soát") {
                        window.open("/tao-bo-thanh-toan-luong", "_self");
                    } else {
                        window.open("/doi-soat/bo-doi-soat/luong/"+ajaxParams['phap_nhan']+"/"+row.getCell("thang").getValue()+"/"+row.getCell("nam").getValue(), "_self");
                    }
                }
            }
            return;
        },
        rowContext:function(e, row){
            //e - the click event object
            //row - row component

            e.preventDefault(); // prevent the browsers default context menu form appearing.
        },ajaxRequesting:function(url, params){
            //url - the URL of the request
            //params - the parameters passed with the request
            $("#loader").removeClass("fadeOut");
            $("#loader").addClass("fadeIn");
            $("#loader").css("opacity", 0.5);
        },ajaxResponse:function(url, params, response){
            //url - the URL of the request
            //params - the parameters passed with the request
            //response - the JSON object returned in the body of the response.
            for(var i in response) {
                if (response[i]['id'] == undefined) {
                    response[i] = {
                        "id" : null,
                        "thang" : response[i].thang,
                        "is_salary" : response[i].is_salary == 0 ? "Ngoài lương" : "Lương",
                        "statusTCB" : "Chưa đối soát",
                        "statusAccounter" : "Chưa đối soát",
                        "nam" : response[i].nam
                    };
                } else {
                    response[i] = {
                        "thang" : response[i].thang,
                        "nam" : response[i].nam,
                        "is_salary" : response[i].is_salary == 0 ? "Ngoài lương" : "Lương",
                        "statusTCB" : response[i].countCross == null ? "Chưa đối soát" : response[i].countUnDone == null ? "Đã đối soát" : "Đang lưu tạm",
                        "statusAccounter" : response[i].ke_toan_check == 1 && response[i].countCross !== null && response[i].countUnDone == null ? "Đã đối soát" : "Chưa đối soát"
                    };
                }
            };
            $("#loader").addClass("fadeOut");
            $("#loader").removeClass("fadeIn");
            return response; //return the response data to tabulator
        },ajaxError:function(xhr, textStatus, errorThrown){
            //xhr - the XHR object
            //textStatus - error type
            //errorThrown - text portion of the HTTP status
            $("#loader").addClass("fadeOut");
            $("#loader").removeClass("fadeIn");
        },renderComplete:function(data){
            resizeHeight();
        },
    });
</script>
@endSection()