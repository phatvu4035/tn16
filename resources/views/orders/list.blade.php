@extends('layouts.app')
<style>
</style>
@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            @if(Topica::can('edit.order'))
                <div id="checkRole" data-check-edit="edit.order"></div>
            @endif
            @if(Topica::can('edit.order.self'))
                    <div id="checkRole" data-check-edit="edit.order.self"></div>
                    <div id="me" data-me="{{Auth::user()->id}}"></div>
            @endif
            <h3 class="c-grey-900">Danh sách bộ thanh toán</h3>
            <div class="form-row">
                <div class="col col-md-4 search-section">
                    <input type="text" id="search-value" class="form-control"
                           placeholder="Nhập mã thanh toán/nội dung/serial" aria-label="Tìm kiếm thông tin"
                           aria-describedby="basic-addon2">
                </div>
                <div class="col col-md-5 search-section">
                    <button id="search-data" class="btn btn-primary" type="button">Tìm kiếm</button>
                    <button class="btn btn-danger d-none" type="button" id="cancel-search">Hủy</button>
                </div>
                @if(Topica::can('add.order'))
                    <div class="col col-md-3 search-section text-right group-btn">
                        <a href="{{route('order.create')}}" id="search-data" class="btn btn-primary border"><i
                                    class="fa fa-plus" style="line-height: 1.5"></i></a>
                    </div>
                @endif
            </div>
            <div class="form-row">
            <!-- <div class="col col-md-1">
                <?=
            Form::inputField([
                'label' => 'Số trang',
                'name' => 'page_size',
                'value' => 1,
                'options' => [
                    'class' => 'page_size'
                ],
            ]);
            ?>
                    </div> -->
                <div id="list-orders-table" class="tabulator table-bordered"></div>
                <div id="pagination-list-orders-table" class="align-center tabulator-pagination"
                     style="display: none;"></div>
            </div>
        </div>
    </div>
@endsection