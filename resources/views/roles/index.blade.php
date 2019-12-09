@extends('layouts.app')

@section('custom-css')
    <link rel="stylesheet" type="text/css" href=" {{ asset('css/role.css') }} ">
@endsection

@section('content')
    <div class="bgc-white p-20 bd">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="c-grey-900 mT-10 mB-30">Danh sách quyền</h3>
                </div>
                <div class="col-md-4">
                    <div class="item-header">
                        <div class="pull-right">
                            <div class="group-search">
                                <div class=group-btn">
                                    @if(Topica::can('add.role'))
                                        <button type="button" class="btn-alter bg-blue"><a
                                                    href="{{ route('roles.create') }}" title="Add role"> <i
                                                        class="fa fa-plus"></i> </a></button>
                                    @endif
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered" id="searchTable">
                        <thead>
                        <tr>
                            <th scope="col" width="10%">#</th>
                            <th scope="col" width="15%">Tên quyền</th>
                            <th scope="col" width="65%">Mô tả</th>
                            <th scope="col" width="10%">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $entity)
                            <tr>
                                <th scope="row" contenteditable="false" width="10%">{{$loop->index + 1}}</th>
                                <td contenteditable="false" width="15%">{{ $entity->name }} </td>
                                <td contenteditable="false" width="65%">{{ $entity->description }}</td>
                                <td width="10%">
                                    @if(Topica::can('edit.role'))
                                        @if($entity->name !== 'Topican' && $entity->name !== 'Administrator')
                                            <a href="{{ route('roles.edit', $entity->id ) }}" class="btn btn-primary"
                                               title="Chỉnh sửa">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @if(Topica::can('delete.role'))
                                        @if($entity->name !== 'Topican' && $entity->name !== 'Administrator')
                                            <form action="{{ route('roles.delete', $entity->id) }}" method="post"
                                                  style="display: inline-block" class="form-delete">
                                                {{ method_field('delete') }}
                                                {!! csrf_field() !!}
                                                <button class="btn btn-danger" title="Xóa" type="submit"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        $('.form-delete').submit(function (e) {
            let result = confirm("Bạn có chắc muốn xóa?");

            if (!result) {
                e.preventDefault();
            }
        });

    </script>
    <script>


    </script>

@endsection

