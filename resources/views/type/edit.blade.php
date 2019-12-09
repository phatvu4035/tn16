@extends('layouts.app')

@section('content')
	<div class="masonry-item col-md-12 w-100" id="insert-voucher">
		{{Form::openForm('Cập nhật chứng từ',['route'=>['type.update', $data->id],'method'=>'PUT'])}}
		<input type="hidden" name="id" value="{{ $data->id }}">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{{ Form::inputField([
		                'label' => "Tên chứng từ<span class='c-r'>*</span>",
		                'name' => 'name',
		                'value'=> old('name', $data->name ),
		                'options' => [
		                    'placeholder' => 'Tên chứng từ',
		                ],
		            ]) }}
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
		            <input type="hidden" name="title" value="new">
				</div>
			</div>

		</div>

		<button type="submit" class="btn btn-primary" id="btnSave">
            Cập nhật
        </button>
		<a href="{{route('type.index')}}" class="btn btn-danger" id="btnCancel">Quay lại</a>
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