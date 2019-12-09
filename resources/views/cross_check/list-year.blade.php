@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="show-cross-check">
        <div class="bgc-white p-20 bd">
            <h3 class="c-grey-900">Đối soát năm sổ kế toán</h3>
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

            if (pn != null) {
                $("#phap_nhan").empty().append('<option value="'+pn+'">'+pn+'</option>').val(pn).trigger('change');
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
                    };
                },
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        var ajaxParams = {
            "phap_nhan" : null
        };

        $("#phap_nhan").change(function(e) {
            var done = true;
            $.each($(".list-cross-dropdown"), function (i, l) {
                if ($(l).val() == 0)
                    done = false;
            });

            if (done) {
                ajaxParams['phap_nhan'] = $("#phap_nhan").val();
                var url = [location.protocol, '//', location.host, location.pathname].join('');
                history.pushState('data', '', url + "?pre-load-pn=" + ajaxParams['phap_nhan']);
                $("#cross-info-table").tabulator("setData", "/doi-soat-nam", ajaxParams);
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
            // ajaxLoaderLoading: '<div class="loader"></div>',
            columns:[ //Define Table Columns
                {title:"preventShow", field:"id", visible: false},
                {title:"preventShow", field:"nam", visible: false},
                {title:"Năm", field:"nam", width: 200},
                {title:"Trạng thái TCB", field:"doneTCB", visible: true, formatter : function (cell, param){
                        var value = cell.getValue();
                        var data = cell.getRow().getData();

                        if (data.id == -1) {
                            cell.getRow().getElement().css({
                                "background-color": "#ffb3ba"
                            });
                            value = "Chưa đối soát";
                        } else if (data.ke_toan_check == 0) {
                            cell.getRow().getElement().css({
                                "background-color": "#ffffba"
                            });
                            value = "Đang lưu tạm";
                        } else if (data.ke_toan_check == 1) {
                            cell.getRow().getElement().css({
                                "background-color": "#baffc9"
                            });
                            value = "Đã đối soát";
                        } else {
                            value = "Không xác định";
                        }

                        return value;
                    }},
                {title:"Trạng thái kế toán", field:"doneAccounter", visible: true, formatter : function (cell, param){
                        var value = cell.getValue();
                        var data = cell.getRow().getData();

                        if (data.id == -1) {
                            value = "Chưa đối soát";
                        } else if (data.ke_toan_check == 1) {
                            value = "Đã đối soát";
                        } else {
                            value = "Chưa đối soát";
                        }

                        return value;
                    }},

            ],
            rowClick: function (e, row) {
                data = row.getData();
                window.open("/doi-soat-nam/"+ajaxParams['phap_nhan']+"/"+data.nam , "_self");
            },
            rowContext:function(e, row){
                //e - the click event object
                //row - row component

                e.preventDefault(); // prevent the browsers default context menu form appearing.
            },ajaxResponse:function(url, params, response){
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
                return response; //return the response data to tabulator
            },ajaxRequesting:function(url, params){
                //url - the URL of the request
                //params - the parameters passed with the request
                $("#loader").removeClass("fadeOut");
                $("#loader").addClass("fadeIn");
                $("#loader").css("opacity", 0.5);
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