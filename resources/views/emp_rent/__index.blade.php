@extends('layouts.app')
<style>
    #pagination-list-orders-table button {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: 6px 12px;
        padding: .375rem .75rem;
        font-size: 14px;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .25rem;
        -webkit-transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        -o-transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    }

    #pagination-list-orders-table button:not([disabled]) {
        cursor: pointer;
    }

    #pagination-list-orders-table .active {
        background: #2196f3;
    }

    #pagination-list-orders-table {
        margin: 0 auto;
    }

    .search-section {
        margin: 20px 0px;
    }
</style>
@section('content')
    <div class="masonry-item col-md-12 w-100" id="insert-voucher">
        <div class="bgc-white p-20 bd">
            <h3 class="c-grey-900">Nhân sự thuê khoán</h3>
            <div class="form-row">
                <div class="col col-md-6 search-section">
                    <div class="input-group mb-3">
                        <input type="text" id="search-value" class="form-control" placeholder="Tìm kiếm thông tin"
                               aria-label="Tìm kiếm thông tin" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button id="search-data" class="btn-outline-secondary btn btn-primary" type="button">Tìm kiếm
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="cancel-search">Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col col-md-6 search-section">
                    <div class="group-btn">
                        <a href="{{route('emp_rent.create')}}" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
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
                <div id="table-list" class="tabulator table-bordered table-list"
                     data-paginator="#pagination-list-orders-table"
                     data-url="{{route('emp_rent.api.list')}}" data-col="{{json_encode(renderTableEmpRent())}}"></div>
                <div id="pagination-list-orders-table" class="align-center"></div>
            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection