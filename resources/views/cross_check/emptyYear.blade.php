@extends('layouts.app')

@section('content')
    <div class="masonry-item col-md-12 w-100" id="show-cross-check">
        <div class="form-row">
            <div class="form-group col-md-6">
                <a class="btn btn-primary" href="{{route('cross_check.listCrossCheckYear', [
                        'pre-load-pn' => $phap_nhan
                    ])}}">Quay lại</a>
            </div>
        </div>
        <div class="bgc-white p-20 bd">
            <h3 class="c-grey-900">Đối soát cho pháp nhân {{$phap_nhan}} năm {{$nam}} sổ kế toán</h3>
            <div class="mT-30">
                <p>Các tháng trong năm {{$nam}} của pháp nhân {{$phap_nhan}} chưa được đối soát đầy đủ hoặc không có dữ liệu.</p>
                <p>Vui lòng nhập liệu thu nhập cho các tháng</p>
                <div class="row">
                    <div class="col col-md-12">
                        <a class="btn btn-primary" href="{{route("cross_check.listCrossCheck", ["pre-load-pn" => $phap_nhan, "pre-load-year" => $nam])}}">Đối soát tháng cho pháp nhân {{$phap_nhan}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endSection()
@section("script")
@endSection()