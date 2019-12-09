@extends('layouts.app')
@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        @php
            if(old()&&!$data){
                $data = old();
            }
        @endphp
        <div class="bgc-white p-20 bd">
            @if(isset($isImportSalary) && $isImportSalary)
                <h3 class="c-grey-900">Tạo bộ thanh toán lương</h3>
                <i>Bạn cần tạo thông tin <b>bộ thanh toán</b> trước khi import <b>bảng lương</b></i>
            @else
                <h3 class="c-grey-900">Tạo bộ thanh toán</h3>
                <i>Bạn cần tạo thông tin <b>bộ thanh toán</b> trước khi thêm <b>chứng từ</b></i>
            @endif

            <div class="mT-30">
                {{Form::openForm('',['method'=>'GET','route'=>'order.insert','id'=>'form-import'])}}
                <div class="form-row">
                    <div style="display: none;">
                        {{
                            Form::inputField([
                                'label' => 'Bộ tạm',
                                'name' => 'temp_order',
                                'value' => isset($data['temp_order']) ? "true" : "false",
                                'options' => [
                                    'class' => 'hidden'
                                ],
                            ])
                        }}
                        {{
                            isset($data['additional_order']) ?
                            Form::inputField([
                                'label' => 'Bộ tạm',
                                'name' => 'additional_order',
                                'value' => isset($data['additional_order']) ? $data['additional_order'] : null,
                                'options' => [
                                    'class' => 'hidden'
                                ],
                            ]) : ""
                        }}
                        {{
                            Form::inputField([
                                'label' => 'Bộ tạm',
                                'name' => 'cross_id',
                                'value' => isset($data['cross_id']) ? $data['cross_id'] : null,
                                'options' => [
                                    'class' => 'hidden'
                                ],
                            ])
                        }}
                        {{
                            Form::inputField([
                                'label' => 'Bộ tạm',
                                'name' => 'cross_check_info_id',
                                'value' => isset($data['cross_check_info_id']) ? $data['cross_check_info_id'] : null,
                                'options' => [
                                    'class' => 'hidden'
                                ],
                            ])
                        }}
                    </div>
                    <div class="form-group col-md-2">
                        {{
                            Form::inputField([
                                'label' => 'Mã osscar',
                                'name' => 'ma_osscar',
                                'value'=>isset($data['order']['ma_osscar'])?$data['order']['ma_osscar']:"",
                                'options' => [
                                    'placeholder' => 'Nhập mã osscar.',
                                    'class' => 'ma_osscar'
                                ],
                            ])
                        }}
                    </div>
                    <div class="form-group col-md-2">
                        {{
                            Form::inputField([
                                'label' => 'Mã dự toán <span class="c-r">*</span>',
                                'name' => 'ma_du_toan',
                                'value'=>isset($data['order']['ma_du_toan'])?$data['order']['ma_du_toan']:"",
                                'options' => [
                                    'placeholder' => 'Nhập mã dự toán.',
                                    'class' => 'ma_du_toan',
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
                        {{--'value'=>isset($data['order']['san_pham'])?$data['order']['san_pham']:"",--}}
                        {{--'options' => [--}}
                        {{--'placeholder' => 'Nhập mã sản phẩm.',--}}
                        {{--'class' => 'san_pham',--}}
                        {{--'required' => true--}}
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
                                'label' => 'Số serial <span class="c-r">*</span>',
                                'name' => 'serial',
                                'value'=>isset($data['order']['serial'])?$data['order']['serial']:"",
                                'options' => [
                                    'class' => 'serial',
                                    'placeholder' => 'Nhập số serial'
                                ],
                            ])
                        }}
                    </div>
                    <div class="form-group col-md-3">
                        {{--{{--}}
                        {{--Form::inputField([--}}
                        {{--'label' => 'Pháp nhân <span class="c-r">*</span>',--}}
                        {{--'name' => 'phap_nhan',--}}
                        {{--'value'=>isset($data['order']['phap_nhan'])?$data['order']['phap_nhan']:"",--}}
                        {{--'options' => [--}}
                        {{--'class' => 'phap_nhan',--}}
                        {{--'placeholder' => 'Nhập mã pháp nhân',--}}
                        {{--'required' => true--}}
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
                                    'required' => true
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
                        {{--'value'=>isset($data['order']['nguoi_de_xuat'])?$data['order']['nguoi_de_xuat']:"",--}}
                        {{--'options' => [--}}
                        {{--'placeholder' => 'Nhập người đề xuất. VD: Hoangld2',--}}
                        {{--'class' => 'nguoi_de_xuat',--}}
                        {{--'required' => true--}}
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
                        {{--'value'=>isset($data['order']['phong_ban'])?$data['order']['phong_ban']:"",--}}
                        {{--'options' => [--}}
                        {{--'placeholder' => 'Nhập phòng ban. VD: TOPICA',--}}
                        {{--'class' => 'phong_ban',--}}
                        {{--'required' => true--}}
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
                            'label' => 'Ngày nhận bộ F <span class="c-r">*</span>',
                            'name' => 'ngay_de_xuat',
                            'value'=>isset($data['order']['ngay_de_xuat'])?$data['order']['ngay_de_xuat']:date("d/m/Y"),
                            'options' => [
                                'placeholder' => 'Nhập ngày đề xuất.',
                                'placeholder' => '',
                                'class' => 'form-group start-date',
                                'data-provide'=>"datepicker",
                                'required' => true
                            ],
                        ]) }}
                    </div>
                    <div class="form-group col-md-3">
                        {{
                            Form::inputField([
                                'label' => 'Người hưởng',
                                'name' => 'nguoi_huong',
                                'value'=>isset($data['order']['nguoi_huong'])?$data['order']['nguoi_huong']:"",
                                'options' => [
                                    'placeholder' => 'Nhập tên người hưởng. VD: hoangld2',
                                    'class' => 'nguoi_huong'
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
                                'value'=>isset($data['order']['noi_dung'])?$data['order']['noi_dung']:(isset($_GET['noi_dung']) ? $_GET['noi_dung'] : (isset($data['noi_dung'])?$data['noi_dung']:"")),
                                'options' => [
                                    'placeholder' => 'Nhập nội dung đề xuất VD: Thưởng nóng dự án cho bà Lê Thị A',
                                    'class' => 'noi_dung',
                                    'required' => true
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
                                'value'=>isset($data['order']['so_tien'])?$data['order']['so_tien']:"",
                                'options' => [
                                    'placeholder' => 'Nhập số tiền',
                                    'class' => 'so_tien',
                                    'required' => true
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
                                'selected'=>isset($data['order']['loai_tien'])?$data['order']['loai_tien']:"VND",
                                'options' => [
                                    'id' => 'loai_tien',
                                    'class' => 'loai_tien',
                                    'required' => true
                                ],
                            ])
                        }}
                    </div>
                    <div class="form-group col-md-3">
                        {{
                            Form::inputField([
                                'label' => 'Tỷ giá <span class="c-r">*</span>',
                                'name' => 'ty_gia',
                                'value'=>isset($data['order']['ty_gia'])?$data['order']['ty_gia']:"1",
                                'options' => [
                                    'placeholder' => 'Nhập tỷ giá quy đổi ra VNĐ. VD: 20,000',
                                    'class' => 'ty_gia',
                                    'readonly' => true,
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
                                'value'=>isset($data['order']['quy_doi'])?$data['order']['quy_doi']:"",
                                'options' => [
                                    'class' => 'quy_doi currency-mask',
                                    'readonly' => true
                                ],
                            ])
                        }}
                    </div>
                </div>
                <hr class="hr-text" data-content="">
                @if(isset($isImportSalary) && $isImportSalary)
                    <input type="hidden" name="import" value="{{json_encode($data['import'])}}">
                    <input type="hidden" name="view" value="import-salary">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <button type="submit" value="insert" data-action="{{route('order.import.file')}}"

                                    class="btn btn-danger" id="importSalary"><i class="fa fa-save"></i> Import bảng
                                lương
                            </button>
                        </div>
                    </div>
                @else
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="submit" name="action-type" id="btnInsertFtt" value="insert"
                                    class="btn btn-danger"><i
                                        class="fa fa-save"></i> Nhập liệu chứng từ
                            </button>
                            <button type="submit" name="action-type" data-action="{{route('order.import.ftt')}}"
                                    id="btnImportFTT" value="import" class="btn btn-primary"><i class="fa fa-save"></i>
                                Import chứng từ
                            </button>
                        </div>
                    </div>
                @endif
                {{Form::closeForm()}}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#phong_ban").select2({
            placeholder: 'Chọn phòng ban',
            language: "vi",
            ajax: {
                url: '/dm4c/cdt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        $("#san_pham").select2({
            language: "vi",
            placeholder: 'Chọn sản phẩm',
            ajax: {
                url: '/dm4c/sp',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        $("#phap_nhan").select2({
            language: "vi",
            placeholder: 'Chọn pháp nhân',
            ajax: {
                url: '/dm4c/pt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        $("#nguoi_de_xuat").select2({
            language: "vi",
            placeholder: 'Chọn người đề xuất',
            ajax: {
                url: '/employee/get',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        var pointer = '<b role="presentation"></b>';

        @if(isset($data['phap_nhan']))
        $("#phap_nhan+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/pt/{{$data['phap_nhan']}}",
            success: function (e) {
                $("#phap_nhan").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#phap_nhan+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf
        @if(isset($data['order']['phap_nhan']))
        $("#phap_nhan+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/pt/{{$data['order']['phap_nhan']}}",
            success: function (e) {
                $("#phap_nhan").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#phap_nhan+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf
        @if(isset($data['nguoi_de_xuat']))

        $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/employee/get-single/{{$data['nguoi_de_xuat']}}",
            success: function (e) {
                $("#nguoi_de_xuat").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf
        @if(isset($data['order']['nguoi_de_xuat']))

        $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/employee/get-single/{{$data['order']['nguoi_de_xuat']}}",
            success: function (e) {
                $("#nguoi_de_xuat").empty().append('<option value="' + e.results.id + '">' + e.results.text + '</option>').val(e.results.id).trigger('change');
                $("#nguoi_de_xuat+.select2-container .select2-selection__arrow").html(pointer);
                $("#btnCancel").hide();
            },
        });
        @endIf
        @if(isset($data['san_pham']))
        //<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>
        $("#san_pham+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/sp/{{$data['san_pham']}}",
            success: function (e) {
                $("#san_pham").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#san_pham+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf
        @if(isset($data['order']['san_pham']))
        //<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>
        $("#san_pham+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/sp/{{$data['order']['san_pham']}}",
            success: function (e) {
                $("#san_pham").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#san_pham+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf
        @if(isset($data['phong_ban']))
        $("#phong_ban+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/cdt/{{$data['phong_ban']}}",
            success: function (e) {
                $("#phong_ban").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#phong_ban+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf
        @if(isset($data['order']['phong_ban']))
        $("#phong_ban+.select2-container .select2-selection__arrow").html('<i class="fa fa-circle-o-notch fa-spin" style="font-size: 12px;margin-top: 10px;"></i>');
        $.ajax({
            url: "/dm4c/cdt/{{$data['order']['phong_ban']}}",
            success: function (e) {
                $("#phong_ban").empty().append('<option value="' + e.results.id + '">' + e.results.id + '</option>').val(e.results.id).trigger('change');
                $("#phong_ban+.select2-container .select2-selection__arrow").html(pointer);
            },
        });
        @endIf

        $('#importSalary').click(function (e) {
            e.preventDefault();
            var action = $('#form-import').attr('action');
            if (validateForm()) {
                $('#form-import').attr('action', $('#importSalary').data('action'));
                $('#form-import').attr('method', 'POST');
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
                        "serial": $('#serial').val()
                    },
                    success: function (r) {
                        if (r.status == 1) {
                            if (r.message) {
                                makeAlert('Thất bại', r.message, 'danger');
                                $('#form-import').attr('action', action);
                                $('#form-import').attr('method', 'GET');
                            } else {
                                $('#form-import').submit();
                            }
                        } else {
                            $('#form-import').attr('action', action);
                            $('#form-import').attr('method', 'GET');
                            makeAlert('Thất bại', r.message, 'danger')
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

        $('#btnImportFTT').click(function (e) {
            e.preventDefault();
            var action = $('#form-import').attr('action');
            if (validateForm()) {
                $('#form-import').attr('action', $(this).data('action'));
                $('#form-import').attr('method', 'POST');
                // kiểm tra seri đã tồn tại trong hệ thống
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
                        "serial": $('#serial').val()
                    },
                    success: function (r) {
                        if (r.status == 1) {
                            if (r.message) {
                                makeAlert('Thất bại', r.message, 'danger');
                                $('#form-import').attr('action', action);
                                $('#form-import').attr('method', 'GET');
                            } else {
                                $('#form-import').submit();
                            }
                        } else {
                            $('#form-import').attr('action', action);
                            $('#form-import').attr('method', 'GET');
                            makeAlert('Thất bại', r.message, 'danger')
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
        $('#btnInsertFtt').click(function (e) {
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
                        "serial": $('#serial').val()
                    },
                    success: function (r) {
                        if (r.status == 1) {
                            if (r.message) {
                                makeAlert('Thất bại', r.message, 'danger');
                            } else {
                                $('#form-import').submit();
                            }
                        } else {
                            makeAlert('Thất bại', r.message, 'danger')
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

        function validateNotNull(id, text) {
            var check = true;
            $('#small-' + id).html("");
            var value = $('#' + id).val();
            if (typeof value === 'string' || value instanceof String) {
                $('#' + id).val(value.trim());
            }
            if (!$('#' + id).val()) {
                check = false;
                $('#small-' + id).html(text + " không được để trống.");
            }
            return check;
        }

        function getFormData($form) {
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function (n, i) {
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        }

        // $(document).ready(function (e) {
        //     $("#form-import").submit(function (e) {
        //         console.log(getFormData($(this)));
        //         e.preventDefault();
        //     })
        // });
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

        $(document).ready(function () {
            if ($("#loai_tien").val() == "VND") {
                $("#ty_gia").prop("readonly", true);
                // $("#ty_gia").val(1);
            } else {
                $("#ty_gia").prop("readonly", false);
                // $("#ty_gia").val("");
            }

            maskMoney();
            calculatePrice();
        });
        maskMoney();
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
        $("#so_tien, #ty_gia").on("change, keyup", function (e) {
            calculatePrice();
        });

        $('#quy_doi').inputmask("numeric", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            removeMaskOnSubmit: true,
            prefix: "VNĐ ", //Space after $, this will not truncate the first character.
            rightAlign: false,
            allowMinus: true,
            oncleared: function () {
                $(this).val(0);
            }
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
                prefix: "VNĐ ", //Space after $, this will not truncate the first character.
                rightAlign: false,
                allowMinus: true,
                oncleared: function () {
                    $(this).val(0);
                }
            });
        }
    </script>
@endsection