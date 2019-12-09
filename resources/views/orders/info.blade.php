{{--@extends('layouts.app')--}}
@extends((( Request::ajax()) ? 'layouts.ajax' : 'layouts.app' ))
@section('content')
    @php($typeEdit = (isset($type)&&$type=='edit'))
    @php($readyonly = !$typeEdit)
    @if($readyonly)
        @if( !Request::ajax())
            <div class="masonry-item col-md-12"></div>
            <div class="masonry-item col-md-12">
                <?php
                $href = isset($requestData['back']) ? $requestData['back'] : route('order.listOrders');
                ?>
                <a class="btn btn-primary" id="btnBack" href="{{$href}}">Quay lại
                </a>
            </div>
        @endIf
    @endif
    @php($readyonly=false)
    @if( !Request::ajax())
        <div class="masonry-item col-md-12 w-100" id="insert-voucher" style="margin-top: 10px">
            <div class="bgc-white p-20 bd">
                @endIf
                <div class="row">
                    <div class="col col-md-6">
                        @if( !Request::ajax())
                            <h3 class="c-grey-900">Thông tin bộ thanh toán F-{{$data->id}}</h3>
                        @endIf
                    </div>
                    @if( !Request::ajax())
                        @if(Topica::can('delete.order'))
                            <div class="col col-md-6 text-right">
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                        data-target="#removeOrder"><i
                                            class="fa fa-times"></i> Hủy bộ thanh toán
                                </button>
                            </div>
                        @endif
                    @endIf
                </div>
                <div class="mT-30">
                    {{Form::openForm('',['method'=>'POST', 'id'=>'form-import','route'=>['order.updateOrderInfo',$data->id]])}}
                    <input type="hidden" name="id" value="{{$data->id}}">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            {{
                                Form::inputField([
                                    'label' => 'Mã osscar',
                                    'name' => 'ma_osscar',
                                    'value' => $data->ma_osscar,
                                    'options' => [
                                        'placeholder' => 'Nhập mã osscar.',
                                        'class' => 'ma_osscar',
                                        'readonly' => $readyonly || Request::ajax()
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-2">
                            {{
                                Form::inputField([
                                    'label' => 'Mã dự toán <span class="c-r">*</span>',
                                    'name' => 'ma_du_toan',
                                    'value' => $data->ma_du_toan,
                                    'options' => [
                                        'placeholder' => 'Nhập mã dự toán.',
                                        'class' => 'ma_du_toan',
                                        'readonly' => $readyonly || Request::ajax(),
                                        'required' => true
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-2">
                            {{--{{--}}
                            {{--Form::inputField([--}}
                            {{--'label' => 'Sản phẩm <span class="c-r">*</span>',--}}
                            {{--'name' => 'san_pham',--}}
                            {{--'value' => $data->san_pham,--}}
                            {{--'options' => [--}}
                            {{--'placeholder' => 'Nhập mã sản phẩm.',--}}
                            {{--'class' => 'san_pham',--}}
                            {{--'required' => true,--}}
                            {{--'readonly' => $readyonly--}}
                            {{--],--}}
                            {{--])--}}
                            {{--}}--}}
                            {{
                                Form::dropDown([
                                    'label' => 'Sản phẩm <span class="c-r">*</span>',
                                    'name' => 'san_pham',
                                    'data' => [],
                                    'noDefault' => true,
                                    'options' => [
                                        'id' => 'san_pham',
                                        'class' => 'san_pham',
                                        'required' => true
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{
                                Form::inputField([
                                    'label' => 'Số serial',
                                    'name' => 'serial',
                                    'value' => $data->serial,
                                    'options' => [
                                        'class' => 'serial',
                                        'placeholder' => 'Nhập số serial',
                                        'readonly' => $readyonly || Request::ajax() || $data->status == \App\Models\OrderInfo::CROSS_CHECK_DONE
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{--{{--}}
                            {{--Form::inputField([--}}
                            {{--'label' => 'Pháp nhân <span class="c-r">*</span>',--}}
                            {{--'name' => 'phap_nhan',--}}
                            {{--'value' => $data->phap_nhan,--}}
                            {{--'options' => [--}}
                            {{--'class' => 'phap_nhan',--}}
                            {{--'placeholder' => 'Nhập mã pháp nhân',--}}
                            {{--'required' => true,--}}
                            {{--'readonly' => $readyonly--}}
                            {{--],--}}
                            {{--])--}}
                            {{--}}--}}
                            {{
                                Form::dropDown([
                                    'label' => 'Pháp nhân <span class="c-r">*</span>',
                                    'name' => 'phap_nhan',
                                    'data' => [],
                                    'noDefault' => true,
                                    'options' => [
                                        'id' => 'phap_nhan',
                                        'class' => 'phap_nhan',
                                        'required' => true  ,
                                        'disabled'=>$data->isSalary==1?true:false
                                    ],
                                ])
                            }}
                        </div>
                    </div>
                    <hr class="hr-text" data-content="NỘI DUNG ĐỀ XUẤT">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {{--{{--}}
                            {{--Form::inputField([--}}
                            {{--'label' => 'Người đề xuất <span class="c-r">*</span>',--}}
                            {{--'name' => 'nguoi_de_xuat',--}}
                            {{--'value' => $data->nguoi_de_xuat,--}}
                            {{--'options' => [--}}
                            {{--'placeholder' => 'Nhập người đề xuất. VD: Hoangld2',--}}
                            {{--'class' => 'nguoi_de_xuat',--}}
                            {{--'required' => true,--}}
                            {{--'readonly' => $readyonly--}}
                            {{--],--}}
                            {{--])--}}
                            {{--}}--}}
                            {{
                                Form::dropDown([
                                    'label' => 'Người đề xuất <span class="c-r">*</span>',
                                    'name' => 'nguoi_de_xuat',
                                    'data' => [],
                                    'noDefault' => true,
                                    'options' => [
                                        'id' => 'nguoi_de_xuat',
                                        'class' => 'nguoi_de_xuat',
                                        'required' => true
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{--{{--}}
                            {{--Form::inputField([--}}
                            {{--'label' => 'Phòng ban <span class="c-r">*</span>',--}}
                            {{--'name' => 'phong_ban',--}}
                            {{--'value' => $data->phong_ban,--}}
                            {{--'options' => [--}}
                            {{--'placeholder' => 'Nhập phòng ban. VD: TOPICA',--}}
                            {{--'class' => 'phong_ban',--}}
                            {{--'required' => true,--}}
                            {{--'readonly' => $readyonly--}}
                            {{--],--}}
                            {{--])--}}
                            {{--}}--}}
                            {{
                                Form::dropDown([
                                    'label' => 'Phòng ban <span class="c-r">*</span>',
                                    'name' => 'phong_ban',
                                    'data' => [],
                                    'noDefault' => true,
                                    'options' => [
                                        'id' => 'phong_ban',
                                        'class' => 'phong_ban',
                                        'required' => true
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::inputField([
                                'label' => 'Ngày đề xuất <span class="c-r">*</span>',
                                'name' => 'ngay_de_xuat',
                                'value' => date_format(date_create($data->ngay_de_xuat), "d/m/Y"),
                                'options' => [
                                    'placeholder' => 'Nhập ngày đề xuất.',
                                    'placeholder' => '',
                                    'class' => 'form-group start-date',
                                    'data-provide'=>"datepicker",
                                    'required' => true,
                                    'disabled' => $readyonly || Request::ajax()
                                ],
                            ]) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{
                                Form::inputField([
                                    'label' => 'Người hưởng',
                                    'name' => 'nguoi_huong',
                                    'value' => $data->nguoi_huong,
                                    'options' => [
                                        'placeholder' => 'Nhập tên người hưởng. VD: hoangld2',
                                        'class' => 'nguoi_huong',
                                        'required' => false,
                                        'readonly' => $readyonly || Request::ajax()
                                    ],
                                ])
                            }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            {{
                                Form::inputTextarea([
                                    'label' => 'Nội dung đề xuất <span class="c-r">*</span>',
                                    'name' => 'noi_dung',
                                    'value' => $data->noi_dung,
                                    'options' => [
                                        'placeholder' => 'Nhập nội dung đề xuất VD: Thưởng nóng dự án cho bà Lê Thị A',
                                        'class' => 'noi_dung',
                                        'value' => $data->noi_dung,
                                        'required' => true,
                                        'readonly' => $readyonly || Request::ajax()
                                    ],
                                ])
                            }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {{
                                Form::inputField([
                                    'label' => 'Số tiền <span class="c-r">*</span>',
                                    'name' => 'so_tien',
                                    'value' => $data->so_tien,
                                    'options' => [
                                        'placeholder' => 'Nhập số tiền',
                                        'class' => 'so_tien',
                                        'required' => true,
                                        'readonly' => $readyonly || Request::ajax(),
                                        'id'=>'so_tien'
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{
                                Form::dropDown([
                                    'label' => 'Loại tiền <span class="c-r">*</span>',
                                    'name' => 'loai_tien',
                                    'data' => array_flip(config('global.accepted_currency')),
                                    'selected' => $data->loai_tien,
                                    'options' => [
                                        'id' => 'loai_tien',
                                        'class' => 'loai_tien',
                                        'required' => true,
                                        'disabled' => $readyonly || Request::ajax()
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{
                                Form::inputField([
                                    'label' => 'Tỷ giá <span class="c-r">*</span>',
                                    'name' => 'ty_gia',
                                    'value' => '1',
                                    'value' => $data->ty_gia,
                                    'options' => [
                                        'placeholder' => 'Nhập tỷ giá quy đổi ra VNĐ. VD: 20,000',
                                        'class' => 'ty_gia',
                                        'readonly' => true || Request::ajax(),
                                        'value' => '1'
                                    ],
                                ])
                            }}
                        </div>
                        <div class="form-group col-md-3">
                            {{
                                Form::inputField([
                                    'label' => 'Quy đổi',
                                    'name' => 'quy_doi',
                                    'value' => $data->quy_doi,
                                    'options' => [
                                        'class' => 'quy_doi currency-mask removeMask',
                                        'readonly' => true
                                    ],
                                ])
                            }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            @if( !Request::ajax())
                                @if($readyonly)
                                    @if( !Request::ajax())
                                        <a type="edit" href="{{route('order.editOrderInfo',$data->id)}}"
                                           name="action-type"
                                           value="insert" class="btn btn-primary"><i
                                                    class="fa fa-pencil"></i> Sửa thông tin bộ thanh toán
                                        </a>
                                    @endIf
                                @else
                                    <button type="edit" name="action-type" id="btnUpdate"
                                            value="update" class="btn btn-primary"><i
                                                class="fa fa-save"></i> Cập nhật
                                    </button>
                                    <a type="edit" href="{{route('order.orderInfo',$data->id)}}"
                                       id="btnCancel"
                                       class="btn btn-danger" style="display: none"><i
                                                class="fa fa-times"></i> Khôi phục
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                    {{Form::closeForm()}}
                    <hr class="hr-text" data-content="">
                </div>
                <p><center><h4 class="c-grey-900">Thông tin chứng từ</h4></center></p>
                @if( !Request::ajax())
                    @if($data->isSalary!=1)
                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-primary" href="{{route('order.edit',$data->id)}}"><i
                                            class="fa fa-pencil"></i>
                                    Quản lý thông tin chứng từ</a>
                            </div>
                        </div>
                    @endif
                @endIf
                <div class="mT-30">
                    <div class="form-row">
                        <div id="order-vouchers" class="tabulator table-bordered">

                        </div>
                        <div id="pagination-list-orders-table" class="align-center tabulator-pagination"
                             style="display: none;"></div>
                    </div>
                </div>
                @if( !Request::ajax())
            </div>
        </div>
    @endIf
    <!-- Modal -->
    @if($data->isSalary==1)
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
    @endif
    <div class="modal fade show" id="removeOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" style="max-width: 80%" role="document" aria-hidden="true">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hủy bộ thanh toán</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc muốn hủy bộ thanh toán
                </div>
                <div class="modal-footer">
                    <a type="edit" href="{{route('order.deleteOrder',$data->id)}}"
                       id="btnDelete"
                       class="btn btn-danger"><i
                                class="fa fa-times"></i> Hủy bộ thanh toán
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary" style="display: none" id="btnModel" data-toggle="modal"
            data-target="#infoEmployee">Launch demo modal
    </button>
@endsection
@section("script")
    <script>
        $('.removeMask').inputmask("numeric", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            removeMaskOnSubmit: true,
            rightAlign: false,
            allowMinus: true,
            oncleared: function () {
                $(this).val(0);
            }
        });
        $('#btnCancel').click(function (e) {
            if (!confirm("Dữ liệu của bạn sẽ bị mất nếu ấn OK")) {
                e.preventDefault();
            }
        });

        $(document).ready(function () {
            $('#so_tien').inputmask("numeric", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                removeMaskOnSubmit: true,
                autoGroup: true,
                rightAlign: false,
                allowMinus: true,
                oncleared: function () {
                    $(this).val(0);
                }
            });
        });
        $('#btnUpdate').click(function (e) {
            e.preventDefault();
            if (validateForm()) {
                $.ajax({
                    url: "/check/serial",
                    method: 'POST',
                    dataType: 'json',
                    context: document.body,
                    async: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "serial": $('#serial').val(),
                        'id': $('input[type=hidden][name=id]').val()
                    },
                    success: function (r) {
                        if (r.status == 1) {
                            if (r.message) {
                                makeAlert('Thất bại', r.message, 'danger');
                            } else {
                                $('#form-import').submit();
                            }
                        } else {
                            makeAlert('Thất bại', r.message, 'danger');
                        }
                    },
                    error: function () {
                        console.log('error');
                    }
                })
            } else {
                e.preventDefault();
            }

        });

        function maskMoney() {
            $('#so_tien').inputmask("numeric", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                removeMaskOnSubmit: true,
                autoGroup: true,
                prefix: $("#loai_tien").val() + " ", //Space after $, this will not truncate the first character.
                rightAlign: false,
                allowMinus: true,
                oncleared: function () {
                    $(this).val(0);
                }
            });
        }

        maskMoney();
        $("#so_tien, #ty_gia").on("change, keyup", function (e) {
            calculatePrice();
        });

        $("#loai_tien").change(function (e) {
            if ($("#loai_tien").val() == "VND") {
                $("#ty_gia").prop("readonly", true);
                $("#ty_gia").val(1);
            } else {
                $("#ty_gia").prop("readonly", false);
                $("#ty_gia").val("");
            }

            maskMoney();
            calculatePrice();
        });

        function calculatePrice() {
            var money = $("#so_tien").inputmask('unmaskedvalue') == "" ? 0 : $("#so_tien").inputmask('unmaskedvalue');
            var exchangeRate = $("#ty_gia").inputmask('unmaskedvalue') == "" ? 0 : $("#ty_gia").inputmask('unmaskedvalue');

            $("#quy_doi").val(money * exchangeRate);
            $('#quy_doi').inputmask("numeric", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false,
                allowMinus: true,
                oncleared: function () {
                    $(this).val(0);
                }
            });
        }


        $("#so_tien").inputmask("numeric", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            rightAlign: false,
            allowMinus: true,
            oncleared: function () {
                $(this).val(0);
            }
        });
        var opacity = 1;
        $("#order-vouchers").tabulator({
            ajaxURL: "/vouchers/by-order/{{$data->id}}", //ajax URL
            ajaxParams: ajaxParams, //ajax parameters
            ajaxConfig: "GET", //ajax HTTP request type
            ajaxLoader: false,
            // ajaxLoaderLoading: '<div class="loader"></div>',
            paginationElement: $("#pagination-list-orders-table"),
            pagination: "remote",
            paginationDataReceived: {
                "max_pages": "last_page"
            },
            height: "400px", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
            layout: "fitColumns", //fit columns to width of table (optional)
            columns: [ //Define Table Columns
                {title: "id", field: "id", visible: false},
                {title: "Mã nhận dạng", field: "employee_code"},
                {title: "Loại mã", field: "employee_table", visible: false},
                {
                    title: "Tên nhân viên",
                    field: "name",
                    visible: true,
                    mutator: function (value, data, type, params, component) {
                        if (data['employee_table'] == "employees") {
                            var employee = data[data['employee_table']];
                            return employee['last_name'] + " " + employee['first_name'];
                        } else if (data['employee_table'] == "employee_rent") {
                            var employee_rent = data['employee_rent_with_delete'];
                            return employee_rent['emp_name'];
                        }
                    }
                },
                {
                    title: "Loại chứng từ",
                    field: "payment_type",
                    mutator: function (value, data, type, params, component) {
                        return data['type_name']['name'];
                    }
                },
                {
                    title: "Số tiền",
                    field: "payment_value",
                    visible: true,
                    validator: ["required", "min:1"],
                    formatter: "money",
                    formatterParams: {precision: 0, symbolAfter: true, symbol: " đ"},
                    mutator: function (value, data, type, params, component) {
                        @if($data->isSalary)
                            return data['sum_tnct'];
                        @else
                            return data['tong_tnct'];
                        @endif
                    }
                },
                {
                    title: "Thuế TNCN",
                    field: "tncn",
                    visible: true,
                    validator: ["required", "min:1"],
                    formatter: "money",
                    formatterParams: {precision: 0, symbolAfter: true, symbol: " đ"},
                    mutator: function (value, data, type, params, component) {
                        @if($data->isSalary)
                            return data['sum_thue_tam_trich'];
                        @else
                            return data['thue_tam_trich'];
                        @endif

                    }
                },
                {
                    title: "Thực nhận",
                    field: "sum_thuc_nhan",
                    visible: true,
                    formatter: "money",
                    editor: false,
                    formatterParams: {precision: 0, symbolAfter: true, symbol: " đ"},
                    mutator: function (value, data, type, params, component) {
                        @if($data->isSalary)
                            return data['sum_thuc_nhan'];
                        @else
                            return data['thuc_nhan'];
                        @endif

                    }
                },
                {
                    title: "Trạng Thái",
                    field: "employee_rent_with_delete.deleted_at",
                    visible: false,
                    formatter: function (cell, param) {
                        var value = cell.getValue();
                        if (value) {
                            cell.getRow().getElement().css({
                                "color": "#ddd"
                            });
                        }
                        return value;
                    }
                },
            ], ajaxRequesting: function (url, params) {
                //url - the URL of the request
                //params - the parameters passed with the request
                $("#loader").removeClass("fadeOut");
                $("#loader").addClass("fadeIn");
                $("#loader").css("opacity", 0.5);
                // opacity = 0.5;
            }, ajaxResponse: function (url, params, response) {
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
                if (response['last_page'] == 1) {
                    $("#pagination-list-orders-table").hide();
                } else {
                    $("#pagination-list-orders-table").show();
                }
                return response; //return the response data to tabulator
            }, ajaxError: function (xhr, textStatus, errorThrown) {
                //xhr - the XHR object
                //textStatus - error type
                //errorThrown - text portion of the HTTP status
                $("#loader").addClass("fadeOut");
                $("#loader").removeClass("fadeIn");
            }, rowClick: function (e, row) {
                // $('#infoEmployee').modal('show');
                // $('#infoEmployee').modal('toggle')
                $('#infoEmployee .modal-body').html(row.getData().data);
                $('#btnModel').trigger('click');
            },
        });

        var pointer = '<b role="presentation"></b>';

        $("#san_pham").select2({
            language: "vi",
            placeholder: 'Chọn sản phẩm',
            @if( Request::ajax())
            disabled: true,
            @endIf()
            ajax: {
                url: '/dm4c/sp',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        @if(isset($data->san_pham))
        //<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>
        $("#san_pham+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/sp/{{$data->san_pham}}",
            success: function (e) {
                $("#san_pham").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#san_pham+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf

        $("#phap_nhan").select2({
            language: "vi",
            placeholder: 'Chọn pháp nhân',
            @if( Request::ajax())
            disabled: true,
            @endIf()
            ajax: {
                url: '/dm4c/pt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        @if(isset($data->phap_nhan))

        $("#phap_nhan+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/pt/{{$data->phap_nhan}}",
            success: function (e) {
                $("#phap_nhan").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#phap_nhan+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf

        $("#phong_ban").select2({
            placeholder: 'Chọn phòng ban',
            language: "vi",
            @if( Request::ajax())
            disabled: true,
            @endIf()
            ajax: {
                url: '/dm4c/cdt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        @if(isset($data->phong_ban))

        $("#phong_ban+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/cdt/{{$data->phong_ban}}",
            success: function (e) {
                $("#phong_ban").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#phong_ban+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf

        $("#nguoi_de_xuat").select2({
            language: "vi",
            placeholder: 'Chọn người đề xuất',
            @if( Request::ajax())
            disabled: true,
            @endIf()
            ajax: {
                url: '/employee/get',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        @if(isset($data->nguoi_de_xuat))

        $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/employee/get-single/{{$data->nguoi_de_xuat}}",
            success: function (e) {
                $("#nguoi_de_xuat").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf

        $("form :input").change(function () {
            $("#btnCancel").show();
        });


        function validateForm() {
            var check = true;
            arr = {
                ma_du_toan: "Mã dự toán",
                san_pham: 'Sản phẩm',
                serial: "Serial",
                phap_nhan: "Pháp nhân",
                nguoi_de_xuat: "Người đề xuất",
                phong_ban: "Phòng ban",
                ngay_de_xuat: "Ngày đề xuất",
                noi_dung: "Nội dung",
                so_tien: "Số tiền",
                loai_tien: "Loại tiền",
                ty_gia: "Tỷ giá"
            };
            $.each(arr, function (k, v) {
                var flag = validateNotNull(k, v);
                check = check && flag;
            })

            return check;
        }
    </script>
@endsection