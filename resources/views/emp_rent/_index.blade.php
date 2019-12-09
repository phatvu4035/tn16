@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            <h6 class="c-grey-900">Danh sách nhân sự thuê khoán</h6>

            <div class="mT-30">
                <div class="group-search">
                    <form class="frm-search" action="{{route('emp_rent.index')}}" id="formSearch">
                        <input type="text" class="form-control" name="search"
                               value="@if(isset($getData['search'])&&$getData['search']){{$getData['search']}}@endif"
                               placeholder="Tìm kiếm"
                        />
                        <div class="group-btn">
                            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                        </div>

                    </form>
                    <div class="group-btn">
                        <a href="{{route('emp_rent.create')}}" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="mT-10">
                    @include('includes.component.table',[
                        'header'=>[
                            'emp_name'=>[
                                'name'=>'Tên',
                                'type'=>'string',
                                'class'=>''
                            ],
                            'identity_type'=>[
                                'name'=>'Loại thẻ',
                                'type'=>'option',
                                'option'=>[
                                    'cmt'=>'CMT',
                                    'hc'=>'Hộ chiếu'
                                ],
                                'class'=>''
                            ],
                            'identity_code'=>[
                                'name'=>'Số thẻ',
                                'type'=>'string',
                                'class'=>''
                            ],
                            'emp_code_date'=>[
                                'name'=>'Ngày đăng ký',
                                'type'=>'datetime',
                                'class'=>'text-center'
                            ],
                            'emp_code_place'=>[
                                'name'=>'Nơi đăng ký',
                                'type'=>'string',
                                'class'=>''
                            ],
                            'emp_tax_code'=>[
                                'name'=>'Mã số thuế',
                                'type'=>'string',
                                'class'=>''
                            ],
                            'emp_country'=>[
                                'name'=>'Quốc gia',
                                'type'=>'string',
                                'class'=>''
                            ],
                            'emp_live_status'=>[
                                'name'=>'Tình trạng cư trú',
                                'type'=>'option',
                                'option'=>[
                                    '0'=>'Không cư trú',
                                    '1'=>'Cư trú'
                                ],
                                'class'=>''
                            ],
                            'emp_account_number'=>[
                                'name'=>'Số tài khoản',
                                'type'=>"string",
                                'class'=>''
                            ],
                            'emp_account_bank'=>[
                                'name'=>'Ngân hàng',
                                'type'=>"string",
                                'class'=>''
                            ],
                            'action'=>[
                                'name'=>'',
                                'type'=>'action',
                                'action'=>['edit'=>'emp_rent.edit','delete'=>'emp_rent.destroy'],
                                'class'=>''
                            ]
                        ],
                        'data'=>$data
                    ])
                </div>
            </div>
            <div>
                {{ $data->links('includes.component.pagination') }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#formSearch').submit(function (e) {
            if ($('input[name=search]').val() == '') {
                e.preventDefault();
                window.location.href = '{{route('emp_rent.index')}}';
            }
        });
        $('#form-delete').submit(function (e) {
            if (!confirm("Bạn có chắc chắn muốn xóa dữ liệu này?")) {
                e.preventDefault();
            }
        });
    </script>
@endsection