@extends('layouts.app')

@section('content')
    <!-- <div class="masonry-sizer col-md-6"></div> -->

    @php
        $isEdit = isset($typeView) && $typeView=='edit';
        if(isset($request['phap_nhan'])){
        $phap_nhan = $request['phap_nhan'];
        }

    @endphp
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            <button style="display:none" id="showEditModal" data-toggle='modal' data-target='#editModal'></button>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="hidden" name="phap_nhan" value="{{isset($phap_nhan)?$phap_nhan:''}}">
                    @if($isEdit)

                    @else
                        <a class="btn btn-primary" href="{{route('order.create', $request)}}">Quay lại</a>
                    @endif
                </div>
            </div>
            <h3 class="c-grey-900">Nhập thông tin chứng từ</h3>
            <div class="mT-30">
                <div id="voucher-form">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <?=
                            Form::inputField([
                                'label' => 'Số hộ chiếu / CMND / Mã NV <span class="c-r">*</span>',
                                'name' => 'identity-code',
                                'options' => [
                                    'placeholder' => 'Nhập số hộ chiếu nhân viên/CMND/Mã nhân viên',
                                    'class' => 'identity-code',
                                    'autofocus' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-1">
                            <label style="display:block; height: 21px"></label>
                            <button type="button" class="btn btn-primary" id="search">Tra cứu</button>
                            <?=
                            Form::inputField([
                                'label' => '',
                                'type' => 'hidden',
                                'name' => 'input-status',
                                'value' => 'havent-search',
                                'options' => [
                                    'id' => 'input-status'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

                    <hr id="emp-zone" class="hr-text" data-content="THÔNG TIN NHÂN SỰ">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Tên nhân viên <span class="c-r">*</span>',
                                'name' => 'emp-name',
                                'options' => [
                                    'placeholder' => 'VD : Trần Đại Quang',
                                    'id' => 'emp-name',
                                    'disabled' => true,
                                    'class' => 'emp-required emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Số hộ chiếu / CMND <span class="c-r">*</span>',
                                'name' => 'emp-identity-code',
                                'options' => [
                                    'placeholder' => 'VD : 9743857839457',
                                    'class' => 'emp-identity-code emp-required',
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::dropDown([
                                'label' => 'Loại thẻ <span class="c-r">*</span>',
                                'name' => 'code-type',
                                'disabled-select' => ['mnv'],
                                'data' => [
                                    'CMND' => 'cmt',
                                    'Hộ chiếu' => 'hc',
                                    'Mã nhân viên' => 'mnv'
                                ],
                                'options' => [
                                    'id' => 'code-type',
                                    'disabled' => true,
                                    'class' => 'emp-required emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Ngày cấp',
                                'name' => 'emp-code-date',
                                'options' => [
                                    'placeholder' => 'VD : 15/03/2018',
                                    'disabled' => true,
                                    'class' => 'form-group start-date emp-create',
                                    'data-provide' => "datepicker"
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Nơi cấp',
                                'name' => 'emp-code-place',
                                'options' => [
                                    'placeholder' => 'VD: Hà Nội',
                                    'disabled' => true,
                                    'class' => 'emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Mã số thuế',
                                'name' => 'emp-tax-code',
                                'options' => [
                                    'placeholder' => 'VD: 2875683746',
                                    'disabled' => true,
                                    'class' => 'emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::dropDown([
                                'label' => 'Quốc tịch <span class="c-r">*</span>',
                                'name' => 'emp-country',
                                'selected' => 'VN',
                                'data' => config('global.list_countries'),
                                'options' => [
                                    'id' => 'emp-country',
                                    'disabled' => true,
                                    'class' => 'emp-create emp-required'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputRadio([
                                'label' => 'Cư trú <span class="c-r">*</span>',
                                'name' => 'emp-live-status',
                                'class' => 'col-md-6',
                                'inputClass' => 'emp-create',
                                'data' => [
                                    'Có' => 'yes',
                                    'Không' => 'no'
                                ],
                                'options' => [
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField(['label' => 'Số tài khoản', 'name' => 'emp-account-number',
                                'options' => [
                                    'placeholder' => 'VD: 3847563456883',
                                    'disabled' => true,
                                    'class' => 'emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField(['label' => 'Ngân hàng', 'name' => 'emp-account-bank',
                                'options' => [
                                    'placeholder' => 'VD: TPBank',
                                    'disabled' => true,
                                    'class' => 'emp-create'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <hr id="emp-zone" class="hr-text" data-content="NHẬP THÔNG TIN CHỨNG TỪ">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <?=
                            Form::dropDown([
                                'label' => 'Vị trí hiện tại <span class="c-r">*</span>',
                                'name' => 'emp-postion',
                                'data' => [
                                    'Nhân viên' => 'nv',
                                    'Cộng tác viên' => 'ctv',
                                    'Thuê khoán' => 'tk',
                                ],
                                'options' => [
                                    'id' => 'emp-postion',
                                    'class' => 'emp-required emp-postion',
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::dropDown([
                                'label' => 'Loại chứng từ <span class="c-r">*</span>',
                                'name' => 'payment-type',
                                'data' => $type,
                                'options' => [
                                    'id' => 'payment-type',
                                    'class' => 'payment-type',
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>

                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Số tiền <span class="c-r">*</span>',
                                'name' => 'payment-value',
                                'options' => [
                                    'placeholder' => 'Số tiền thưởng',
                                    'class' => 'payment-value currency-mask',
                                    'id' => 'payment-value',
                                    'disabled' => true,
                                    'value' => 0
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Thuế TNCN <span class="c-r">*</span>',
                                'name' => 'personal-tax',
                                'options' => [
                                    'placeholder' => 'Thuế thu nhập cá nhân',
                                    'id' => 'personal-tax',
                                    'class' => 'payment-value currency-mask',
                                    'disabled' => true,
                                    'value' => 0
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Thực nhận <span class="c-r">*</span>',
                                'name' => 'real-money',
                                'options' => [
                                    'placeholder' => 'Số tiền thực nhận',
                                    'id' => 'real-money',
                                    'disabled' => true,
                                    'class' => 'currency-mask'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <p class="" style="padding-left: 10px;font-weight: bold">Pháp Nhân : <span id="phap_nhan_name">--</span>
                        </p>
                    </div>
                    <div class="form-row">
                        <button id="add-voucher" class="btn btn-primary" disabled>Thêm chứng từ</button>
                    </div>
                </div>

                <div class="form-group">
                    <hr class="hr-text" data-content="THÔNG TIN CHỨNG TỪ">
                </div>
                <div class="table-controls form-row">
                <!-- <div class="col-md-2 invisible">
                    <?=
                Form::dropDown([
                    'label' => 'Cột cần lọc:',
                    'name' => 'filter-col',
                    'data' => [
                        'Tên' => 'emp_name',
                        'Số tiền' => 'payment_value',
                        'Loại thanh toán' => 'payment_type',
                        'Mã nhận dạng' => 'code'
                    ],
                    'options' => [
                        'id' => 'filter-field'
                    ],
                ]);
                ?>
                        </div>
                        <div class="col-md-2 invisible">
<?=
                Form::dropDown([
                    'label' => 'Công thức:',
                    'name' => 'filter-type',
                    'data' => array_flip([
                        '=' => 'bằng',
                        '<' => 'nhỏ hơn',
                        '<=' => 'nhỏ hơn hoặc bằng',
                        '>' => 'lớn hơn',
                        '>=' => 'lớn hơn hoặc bằng',
                        '!=' => 'khác',
                        'like' => 'chứa',
                    ]),
                    'options' => [
                        'id' => 'filter-type'
                    ],
                ]);
                ?>
                        </div>

                        <div class="form-group col-md-3 invisible">
<?=
                Form::inputField([
                    'label' => 'Giá trị lọc',
                    'name' => 'filter-value',
                    'type' => 'text',
                    'value' => '',
                    'options' => [
                        'placeholder' => 'Nhập giá trị cần lọc',
                        'id' => 'filter-value'
                    ],
                ]);
                ?>
                        </div>
                        <div class="form-group col-md-1 invisible">
                            <label style="display:block; height: 21px"></label>
                            <button type="button" class="btn btn-primary" id="filter-clear">Xóa bộ lọc</button>
                        </div>
                        <div class="form-group col-md-3">
                        </div> -->
                </div>
                <div class="row justify-content-between">
                    <div class="form-group col-md-4">
                        <!-- <label style="display:block; height: 21px"></label> -->
                        <span>Số tiền trên bộ thanh toán : <b>{{number_format($request['quy_doi'])}} VNĐ</b></span><br>
                        <span>
                            Tổng số tiền chứng từ 	&nbsp;	&nbsp;	&nbsp;	&nbsp;:
                            <b id="sumVouchers">0 VNĐ</b>
                        </span>
                    </div>
                    <div class="form-group col-md-1">
                        <!-- <label style="display:block; height: 21px"></label> -->
                        <label style="display:block; height: 21px; padding-top: 6px;" id="rows-count"></label>
                    </div>
                </div>
                <div id="order-table" class="tabulator table-bordered"></div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <button class="btn btn-primary" @if($isEdit) data-edit="true"
                                data-url="{{route('order.orderInfo',$request->id)}}" @endif id="save-data"><i
                                    class="fa fa-save"></i> Lưu
                        </button>
                        @if($isEdit)
                            <button class="btn btn-danger" data-toggle="modal" data-target="#confirmCancelEdit"
                                    id="cancel-data"><i class="fa fa-times"></i> Hủy
                            </button>
                        @else
                            <button class="btn btn-danger" data-toggle="modal" data-target="#confirmCancel"
                                    id="cancel-data"><i class="fa fa-times"></i> Hủy
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Chỉnh sửa thông tin thuê khoán</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="edit-emp">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Tên nhân viên',
                                'name' => 'edit-emp-name',
                                'options' => [
                                    'placeholder' => 'VD : Trần Đại Quang',
                                    'id' => 'edit-emp-name',
                                    'disabled' => true,
                                    'class' => 'emp-edit edit-emp-required'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField([
                                'label' => 'Số hộ chiếu / CMND',
                                'name' => 'edit-emp-identity-code',
                                'options' => [
                                    'placeholder' => 'VD : 9743857839457',
                                    'class' => 'emp-identity-code edit-emp-required',
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::dropDown([
                                'label' => 'Loại thẻ',
                                'name' => 'edit-emp-code-type',
                                'disabled-select' => ['mnv'],
                                'data' => [
                                    'CMND' => 'cmt',
                                    'Hộ chiếu' => 'hc',
                                    'Mã nhân viên' => 'mnv'
                                ],
                                'options' => [
                                    'id' => 'edit-emp-code-type',
                                    'disabled' => true,
                                    'class' => 'edit-emp-required'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Ngày cấp',
                                'name' => 'edit-emp-code-date',
                                'options' => [
                                    'placeholder' => 'VD : 15/03/2018',
                                    'disabled' => true,
                                    'class' => 'emp-edit form-group start-date',
                                    'data-provide' => "datepicker"
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?=
                            Form::inputField([
                                'label' => 'Nơi cấp',
                                'name' => 'edit-emp-code-place',
                                'options' => [
                                    'placeholder' => 'VD: Hà Nội',
                                    'disabled' => true,
                                    'class' => 'emp-edit'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <?=
                            Form::dropDown([
                                'label' => 'Quốc tịch',
                                'name' => 'edit-emp-country',
                                'selected' => 'VN',
                                'data' => config('global.list_countries'),
                                'options' => [
                                    'id' => 'edit-emp-country',
                                    'disabled' => true,
                                    'class' => 'emp-edit edit-emp-required'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputRadio([
                                'label' => 'Cư trú',
                                'name' => 'edit-emp-live-status',
                                'class' => 'col-md-6 edit-emp-required',
                                'inputClass' => 'emp-edit',
                                'data' => [
                                    'Có' => 'yes',
                                    'Không' => 'no'
                                ],
                                'options' => [
                                    'disabled' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField(['label' => 'Số tài khoản', 'name' => 'edit-emp-account-number',
                                'options' => [
                                    'placeholder' => 'VD: 3847563456883',
                                    'disabled' => true,
                                    'class' => 'emp-edit'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <?=
                            Form::inputField(['label' => 'Ngân hàng', 'name' => 'edit-emp-account-bank',
                                'options' => [
                                    'placeholder' => 'VD: TPBank',
                                    'disabled' => true,
                                    'class' => 'emp-edit'
                                ],
                            ]);
                            ?>
                            <?=
                            Form::inputField(['label' => '', 'name' => 'edit-index',
                                'type' => 'hidden',
                                'options' => [
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <?=
                            Form::inputField(['label' => 'Mã số thuế', 'name' => 'edit-emp-tax-code',
                                'options' => [
                                    'placeholder' => 'VD: 2875683746',
                                    'disabled' => true,
                                    'class' => 'emp-edit'
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <?=
                            Form::dropDown([
                                'label' => 'Vị trí hiện tại',
                                'name' => 'edit-emp-position',
                                'data' => [
                                    'Nhân viên' => 'nv',
                                    'Cộng tác viên' => 'ctv',
                                    'Thuê khoán' => 'tk',
                                ],
                                'options' => [
                                    'disabled' => true,
                                    'id' => 'edit-emp-position',
                                    'class' => 'emp-position edit-emp-required'
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close-edit" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" id="save-edit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
    <div style="display: none" id="tblData" data-table="{{isset($employeeOrder)?json_encode($employeeOrder):''}}"></div>
    <div style="display: none" id="order" data-order="{{isset($request)?json_encode($request):''}}"></div>
    <div class="modal fade" id="confirmCancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Xác nhận hủy
                </div>
                <div class="modal-body">
                    <p>Mọi dữ liệu của bộ thanh toán này sẽ bị mất sau khi hủy bộ thanh toán.</p>
                    <p>Bạn có chắc chắn muốn hủy bộ thanh toán này ?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="cancel-insert">Đồng ý
                    </button>
                    <button class="btn btn-default" id="" data-dismiss="modal">Bỏ</button>
                </div>
            </div>
        </div>
    </div>
    @if($isEdit)
        <div class="modal fade" id="confirmCancelEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Xác nhận hủy
                    </div>
                    <div class="modal-body">
                        <p>Mọi dữ liệu của bộ thanh toán được thêm mới hoặc chỉnh sửa sẽ bị mất sau khi hủy bộ thanh
                            toán.</p>
                        <p>Bạn có chắc chắn muốn thoát khỏi màn hình này?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="cancel-insert-edit"
                                data-href="{{route('order.orderInfo',$request->id)}}">Đồng ý
                        </button>
                        <button class="btn btn-default" id="" data-dismiss="modal">Bỏ</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @php
        $listType = [];
        foreach ($type as $k=>$v){
            $listType[$k]=$k;
        }
    @endphp
    <script>
        var type = JSON.parse('<?=json_encode(array_flip($type))?>');
        var payment_type = '<?= json_encode(($listType), JSON_UNESCAPED_UNICODE) ?>';
    </script>
@endsection
@section("script")
    <script type="text/javascript" src="{{URL::asset('assets/js/vouchers-table.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#code-type').change(function () {
                $('#small-emp-identity-code').html("");
                $('#add-voucher').prop('disabled', false);
                code_type = $(this).val();
                if (code_type == 'cmt') {
                    leng_emp_identity_code = $('#emp-identity-code').val().length;
                    if (leng_emp_identity_code != 9 && leng_emp_identity_code != 12) {
                        $('#small-emp-identity-code').html("CMT phải có 9 hoặc 12 chữ số");
                        $('#add-voucher').prop('disabled', true);
                    } else {

                    }
                } else {

                }

            });
            $('#search').click(function () {
                $('#small-emp-identity-code').html("");
                $('#add-voucher').prop('disabled', false);
            });
        });
    </script>
@endsection