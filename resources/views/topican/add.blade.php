@extends('layouts.app')

@section('content')
	<div class="masonry-item col-md-12 w-100" id="insert-voucher">
		{{Form::openForm('Thêm tài khoản',['route'=>'topican.store','method'=>'POST'])}}
		<input type="hidden" name="id" value="0">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{{ Form::inputField([
		                'label' => "Tên tài khoản<span class='c-r'>*</span>",
		                'name' => 'name',
		                'value'=> old('name', ''),
		                'options' => [
		                    'placeholder' => 'Tên tài khoản',
		                ],
		            ]) }}
				</div>

	            <div class="form-group">
	            	{{ Form::inputField([
	                    'label' => "Email <span class='c-r'>*</span>",
	                    'name' => 'email',
	                    'value'=>old('email', ''),
	                    'options' => [
	                        'placeholder' => 'Email',
	                    ],
	                ]) }}
	            </div>

                <div class="form-group">
                	{{ Form::inputField([
	                    'label' => 'Mã nhân viên',
	                    'name' => 'employee_code',
	                    'value'=>old('employee_code', ''),
	                    'options' => [
	                        'placeholder' => 'Mã nhân viên',
	                    ],
	                ]) }}
                </div>


			</div>
			
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
						    <label>Avatar</label>
						    <input type="file" class="form-control-file" name="employee_avatar">
						</div>
					</div>
					<div class="col-md-4">
						<div class="item">
							<img src="" alt="">
						</div>
					</div>
				</div>

				@php
					$role_select = [];
					foreach ($roles as $r) {
						$role_select[$r->name] = $r->id;
					}
				@endphp

				<div class="form-group">
					{{Form::dropDown([
	                    'label' => "Quyền<span class='c-r'>*</span>",
	                    'name' => 'role_id',
	                    'selected'=> old('role_id', ''),
	                    'data' => $role_select,
	                    'options' => [
	                        'id' => 'identity_type'
	                    ],
	                ])}}
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-primary" id="btnSave">
            Tạo mới
        </button>
		<a href="{{route('topican.index')}}" class="btn btn-danger" id="btnCancel">Quay lại</a>
        {{Form::closeForm()}}
	</div>

	
	
@endsection

@section('script')
	<script>
		@if ($errors->any())
			@foreach ($errors->all() as $error)
				makeAlert('Lỗi:',"{{ $error }}", 'warning');
			@endforeach
		@endif

		@if(session()->has('error'))
			makeAlert('Lỗi:', "{{ session()->get('error') }}", 'warning' );
		@endif

		@if(session()->has('message'))
			makeAlert('Thành công:', "{{ session()->get('message') }}", 'success');
		@endif

	</script>

@endsection