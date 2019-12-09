@extends('layouts.app')

@section('content')
    <style>
        .w-100px {
            width: 100px;
        }

        #phap_nhan+.select2-container {
            width: 217px !important;
            margin-left: 10px;
        }
    </style>
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @php
                $year = [];
                for($y = 2018;$y<=date('Y');$y++){
                    $year[$y]=$y;
                }
            @endphp
            @include('includes.list',[
                'title'=>'Danh sách thu nhập',
                'filter'=>[
                    [
                        'type'=>"text",
                        'name'=>'search',
                        'placeholder'=>"Tên hoặc Mã NV/CMT/HC"
                    ],
                    [
                        'type'=>"select",
                        'name'=>'phap_nhan',
                        'placeholder'=>"Pháp nhân",
                        'id' => "phap_nhan",
                        'option'=>[]
                    ],
                    [
                        'type'=>"select",
                        'name'=>'month',
                        'placeholder'=>"Tháng",
                        'value'=>"",
                        'option'=>[
                            ''=>'Chọn tháng',
                            '1'=>'Tháng 1',
                            '2'=>'Tháng 2',
                            '3'=>'Tháng 3',
                            '4'=>'Tháng 4',
                            '5'=>'Tháng 5',
                            '6'=>'Tháng 6',
                            '7'=>'Tháng 7',
                            '8'=>'Tháng 8',
                            '9'=>'Tháng 9',
                            '10'=>'Tháng 10',
                            '11'=>'Tháng 11',
                            '12'=>'Tháng 12',
                        ]
                    ],
                    [
                        'type'=>"select",
                        'name'=>'year',
                        'placeholder'=>"Năm",
                        'value'=>date('Y'),
                        'option'=>$year
                    ]
                ]
            ])
            <div class="form-row" style="margin-top: 10px;">
                <div class="col col-md-12">
                    <button class="btn btn-primary" title="Xuất báo cáo" type="submit" id="btnExportExcel">
                        <i class="fa fa-save"></i>
                        Xuất báo cáo
                </div>
            </div>

        </div>
    </div>

@endsection


@section('script')
    <script>
        $(document).ready(function () {
            listTable.init({
                url: '{{route('summary.api.list')}}'
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
        var exportRoute = "{{ route('export.dstn') }}";
        var sCount = 0;

        // Xuat bao cao
        $('#btnExportExcel').click(function () {
            let condition = $('#formSearch').serializeArray();
            var allowExport = false;
            // Bat buoc phai co phap nhan
            for( key in condition ) {
                let input = condition[key];
                if(input.name == 'phap_nhan' && input.value != '') {
                    allowExport = true;
                }
            }

            condition = $('#formSearch').serialize();
            if(allowExport) {
                window.location.href = exportRoute + "?" + condition;
                $(this).prop('disabled', true);
            } else {
                alert('Vui lòng chọn pháp nhân');
            }
            
        });
    </script>
@endsection