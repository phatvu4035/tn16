<div class="container-fluid">
    <table class="table table-responsive table-bordered table-hover">
        <thead>
        <tr>
            <td>Tháng</td>
            <td>Pháp nhân</td>
            <td>Tổng thu nhập trước thuế</td>
            <td>Tổng TN không chịu thuế</td>
            <td>Tổng TNCT</td>
            <td>BHXH</td>
            <td>Thuế tạm trích</td>
            <td>Thực nhận</td>
            <td>Giảm trừ bản thân</td>
            <td>Giảm trừ gia cảnh</td>
            <td>Bộ chứng từ</td>
            <td>Loại chứng từ</td>
            <td>Ngày thanh toán</td>
            <td>Diễn giải</td>
            <td>Trạng thái</td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $d)
            @php($month_year = ($d->status==1||$d->type==1)?$d->month.'/'.$d->year:date('n/Y',strtotime($d->created_at)))
            <tr @if($d->status!=1) class="text-danger" @endif>
                <td>{{$month_year}}</td>
                <td>{{$d->phap_nhan}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_thu_nhap_truoc_thue:$d->tong_thu_nhap_truoc_thue)}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_non_tax:$d->tong_non_tax)}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_tnct:$d->tong_tnct)}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_bhxh:$d->bhxh)}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_thue_tam_trich:$d->thue_tam_trich)}}</td>
                <td class="text-right">{{number_format($d->type == 1?$d->sum_thuc_nhan:$d->thuc_nhan)}}</td>
                <td class="text-right">{{number_format($d->giam_tru_ban_than)}}</td>
                <td class="text-right">{{number_format($d->giam_tru_gia_canh)}}</td>
                <td class="text-right">F-{{$d->order_id}}</td>

                <td class="text-right">{{$d->typeName->name}}</td>
                <td class="text-center">
                @if(strtotime($d->ngay_thanh_toan) && $d->ngay_thanh_toan!="0000-00-00 00:00:00")
                    {{date('d/m/Y',strtotime($d->ngay_thanh_toan))}}
                @endif
                <td class="text-left">
                    {{$d->order->noi_dung}}
                </td>
                <td class="text-right">@if($d->status==1) <a href="javascript:void(0)" class="" title="Đã thanh toán">
                        <i class="fa fa-flag" aria-hidden="true"></i>
                    </a> @else <a href="javascript:void(0)" class="" title="Chờ thanh toán">
                        <i class="fa fa-flag" style="color: red " aria-hidden="true"></i>
                    </a> @endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>