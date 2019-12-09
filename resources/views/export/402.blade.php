@extends('layouts.app')

@section('content')
    <style>
        #small-phap_nhan {
            margin-top: -12px;

        }
    </style>
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <h3 class="c-grey-900">Báo cáo 402</h3>
        <div class="mT-30">
            {{Form::openForm('',['method'=>'POST','route'=>'export.402.post','id'=>'form-402'])}}
            <div class="form-row">
                <div class="form-group col-md-6">
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
                <div class="form-group col-md-6">
                    @php
                    //dd(old('year'));
                        $year = [];
                        for($y = 2018;$y<=date('Y');$y++){
                            $year[$y]=$y;
                        }
                    @endphp
                    {{ Form::dropDown([
                            'label' => 'Năm <span class="c-r">*</span>',
                            'name' => 'year',
                            'data' => array_flip($year),
                            'selected'=>isset($data['year'])?$data['year']:(old('year')?old('year'):date('Y')),
                            //'selected'=>old('year'),
                            'options' => [
                                'id' => 'year',
                            ],
                        ])}}
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <button type="submit" value="download"
                            class="btn btn-primary" id="download401"><i class="fa fa-download"></i> Download
                    </button>
                </div>
            </div>
            <div class="form-row">
                {{--@include('export.components.table401')--}}
            </div>
            {{Form::closeForm()}}
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            // alert(;)
            $('#download401').click(function (e) {
                $('#small-phap_nhan').html('');
                $('#small-month').html('');
                $('#small-year').html('');
            });
        });

        $("#phap_nhan").select2({
            language: "vi",
            placeholder: 'Chọn pháp nhân',
            allowClear : true,
            ajax: {
                url: '/dm4c/pt',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });
    </script>
@endsection