@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        @if($errors->all())
            <div class="form-group">
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        @if(isset($empRent->id) && $empRent->id)
            {{Form::openForm('Sửa nhân sự thuê khoán',['route'=>['emp_rent.update',$empRent->id],'method'=>'PUT','id'=>'from-emp-rent'])}}
            <input type="hidden" name="id" value="{{$empRent->id}}">
        @else
            {{Form::openForm('Thêm mới nhân sự thuê khoán',['route'=>'emp_rent.store','method'=>'POST','id'=>'from-emp-rent'])}}

        @endif


        <div class="form-row">
            <div class="form-group col-md-4">
                {{ Form::inputField([
                            'label' => "Họ và tên <span class='c-r'>*</span>",
                            'name' => 'emp_name',
                            'value'=>(isset($empRent->emp_name)&&$empRent->emp_name)?$empRent->emp_name:"",
                            'options' => [
                                'placeholder' => '',
                                'class' => 'emp_name',
                            ],
                        ])}}
            </div>
            <div class="form-group col-md-4">
                {{ Form::inputField([
                          'label' => 'Quốc tịch',
                          'name' => 'emp_country',
                          'value'=>(isset($empRent->emp_country)&&$empRent->emp_country)?$empRent->emp_country:"",
                          'options' => [
                              'placeholder' => '',
                              'class' => 'emp_country'
                                                         ],
                      ])}}
            </div>
            <div class="form-group col-md-4">
                {{Form::dropDown([
                                 'label' => "Tình trạng cư trú <span class='c-r'>*</span>",
                                 'name' => 'emp_live_status',
                                 'selected'=>(isset($empRent->emp_live_status)&&$empRent->emp_live_status)?$empRent->emp_live_status:"",
                                 'data' => [
                                     'Không cư trú' => 0,
                                     'Cư trú' => 1
                                  ],
                                 'options' => [
                                     'id' => 'emp_live_status'
                                 ],
                             ])}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                {{ Form::inputField([
                          'label' => 'Mã số thuế',
                          'name' => 'emp_tax_code',
                          'value'=>(isset($empRent->emp_tax_code)&&$empRent->emp_tax_code)?$empRent->emp_tax_code:"",
                          'options' => [
                              'placeholder' => '',
                              'class' => 'emp_tax_code'
                                                         ],
                      ])}}
            </div>
            <div class="form-group col-md-4">
                {{ Form::inputField([
                          'label' => 'Số tài khoản',
                          'name' => 'emp_account_number',
                          'value'=>(isset($empRent->emp_account_number)&&$empRent->emp_account_number)?$empRent->emp_account_number:"",
                          'options' => [
                              'placeholder' => '',
                              'class' => 'emp_account_number'
                                                         ],
                      ])}}
            </div>
            <div class="form-group col-md-4">
                {{ Form::inputField([
                          'label' => 'Ngân hàng',
                          'name' => 'emp_account_bank',
                          'value'=>(isset($empRent->emp_account_bank)&&$empRent->emp_account_bank)?$empRent->emp_account_bank:"",
                          'options' => [
                              'placeholder' => '',
                              'class' => 'emp_account_bank'
                                                         ],
                      ])}}
            </div>
        </div>
        <div class="form-row">

        </div>
        <hr class="hr-text" data-content="Thông tin Hộ chiếu / CMT">
        <div class="form-row">
            <div class="form-group col-md-3">
                {{Form::dropDown([
                                'label' => "Loại thẻ <span class='c-r'>*</span>",
                                'name' => 'identity_type',
                                'selected'=>(isset($empRent->identity_type)&&$empRent->identity_type)?$empRent->identity_type:"",
                                'data' => [
                                    'Chứng minh thư' => 'cmt',
                                    'Hộ chiếu' => 'hc'
                                 ],
                                'options' => [
                                    'id' => 'identity_type'
                                ],
                            ])}}
            </div>
            <div class="form-group col-md-3">
                {{ Form::inputField([
                           'label' => "Số thẻ <span class='c-r'>*</span>",
                           'name' => 'identity_code',
                           'value'=>(isset($empRent->identity_code)&&$empRent->identity_code)?$empRent->identity_code:"",
                           'options' => [
                               'placeholder' => '',
                               'class' => 'identity_code',
                           ],
                       ])}}
            </div>
            <div class="form-group col-md-3">
                {{ Form::inputField([
                           'label' => 'Ngày cấp',
                           'name' => 'emp_code_date',
                           'value'=>(isset($empRent->emp_code_date)&&$empRent->emp_code_date)?date('d/m/Y', strtotime($empRent->emp_code_date)):"",
                           'options' => [
                               'placeholder' => '',
                               'class' => 'emp_code_date',
                               "data-provide"=>"datepicker"
                           ],
                       ])}}
            </div>
            <div class="form-group col-md-3">
                {{ Form::inputField([
                           'label' => 'Nơi cấp',
                           'name' => 'emp_code_place',
                           'value'=>(isset($empRent->emp_code_place)&&$empRent->emp_code_place)?$empRent->emp_code_place:"",
                           'options' => [
                               'placeholder' => '',
                               'class' => 'emp_code_place'
                                                          ],
                       ])}}
            </div>
        </div>

        @if(isset($empRent->id) && $empRent->id)
            <button type="submit" class="btn btn-primary" id="btnSave">
                Lưu
            </button>
        @else
            <button type="submit" class="btn btn-primary" id="btnSave">
                Tạo mới
            </button>
        @endif
        <a href="{{route('emp_rent.index')}}" class="btn btn-danger" id="btnCancel">Quay lại</a>
        {{Form::closeForm()}}

    </div>
@endsection

@section('script')
    <script>
        $('#btnCancel').click(function (e) {
            if (!confirm("Dữ liệu nhập của bạn có thể bị  mất nếu ấn Quay lại")) {
                e.preventDefault();
            }
        });
        $('#from-emp-rent').submit(function (e) {
            if (!validateBeforeSave()) {
                console.log(validateBeforeSave());
                e.preventDefault();
            }
        });


        function validateBeforeSave() {
            var emp_name = $('#emp_name').val();
            var emp_live_status = $('#emp_live_status').val();
            var identity_type = $('#identity_type').val();
            var identity_code = $('#identity_code').val();

            var check = true;

            //clear error style
            $('#emp_name').removeClass('errors_style');
            $('#emp_live_status').removeClass('errors_style');
            $('#identity_type').removeClass('errors_style');
            $('#identity_code').removeClass('errors_style');

            if (!emp_name) {
                check = check && false;
                $('#emp_name').addClass('errors_style');
            }
            if (!emp_live_status) {
                check = check && false;
                $('#emp_live_status').addClass('errors_style');
            }
            if (!identity_type) {
                check = check && false;
                $('#identity_type').addClass('errors_style');
            }

            if (!identity_code) {
                check = check && false;
                $('#identity_code').addClass('errors_style');
            }

            return check;
        }
    </script>
@endsection