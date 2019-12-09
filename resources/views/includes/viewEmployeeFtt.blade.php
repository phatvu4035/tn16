<div class="container-fluid">
    <div><span><h3>Các khoản đã đối soát</h3></span></div>
    <table id="cang-vl" class="table table-bordered table-hover">
        <thead>
        <tr>
            <td>Ngày thanh toán</td>
            <td>Tổng TNCT</td>
            <td>Thuế tạm trích</td>
            <td>Thực nhận</td>
            <td>Bộ chứng từ</td>
            <td>Diễn giải</td>
            <td>Trạng thái</td>


        </tr>
        </thead>
        <tbody>
        @php
            $tong_tat_ca_tnct = 0;
            $tong_tat_ca_thue_tam_trich = 0;
            $tong_tat_ca_thuc_nhan = 0;
            $hasSalary=false;
            $listStatusIsZero = [];
        @endphp
        @foreach($data as $d)
            @if($d->type!=1 || !$d->data)
                @php
                    $month_year = ($d->status==1||$d->type==1)?$d->month.'/'.$d->year:date('n/Y',strtotime($d->created_at))
                @endphp
                @if($d->status ==1)
                    <tr @if($d->status!=1) class="text-danger" @endif>
                        <td class="text-center">
                            @if(strtotime($d->ngay_thanh_toan) && $d->ngay_thanh_toan!="0000-00-00 00:00:00")
                                {{date('d/m/Y',strtotime($d->ngay_thanh_toan))}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_tnct:$d->tong_tnct)}}</td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_thue_tam_trich:$d->thue_tam_trich)}}</td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_thuc_nhan:$d->thuc_nhan)}}</td>
                        <td class="text-right">F-{{$d->order_id}}</td>
                        <td class="text-left">
                            {{$d->order->noi_dung}}
                        </td>
                        <td class="text-right">@if($d->status==1) <a href="javascript:void(0)" class=""
                                                                     title="Đã thanh toán">
                                <i class="fa fa-flag" aria-hidden="true"></i>
                            </a> @else <a href="javascript:void(0)" class="" title="Chờ thanh toán">
                                <i class="fa fa-flag" style="color: red " aria-hidden="true"></i>
                            </a> @endif</td>
                        @php
                            if($d->type==1){
                                $hasSalary = true;
                            }
                            if($d->status==1){
                                $tong_tat_ca_tnct+=$d->tong_tnct;
                                $tong_tat_ca_thue_tam_trich +=$d->thue_tam_trich;
                                $tong_tat_ca_thuc_nhan +=$d->thuc_nhan;
                            }
                        @endphp
                        @else
                            @php
                                $listStatusIsZero[] = $d;
                            @endphp
                        @endif
                    </tr>
                @endif
                @endforeach

                <tr class="text-success">
                    <td class="text-center">Tổng</td>
                    <td class="text-right">
                        {{number_format($tong_tat_ca_tnct)}}
                    </td>
                    <td class="text-right">
                        {{number_format($tong_tat_ca_thue_tam_trich)}}
                    </td>
                    <td class="text-right">
                        {{number_format($tong_tat_ca_thuc_nhan)}}
                    </td>
                    <td colspan="3"></td>

                </tr>

        </tbody>
    </table>
    @if($listStatusIsZero && !$hasSalary)
        <div><span><h3>Các khoản chưa đối soát</h3></span></div>
        <table id="cang-vl" class="table table-bordered table-hover">
            <thead>
            <tr>
                <td>Ngày thanh toán</td>
                <td>Tổng TNCT</td>
                <td>Thuế tạm trích</td>
                <td>Thực nhận</td>
                <td>Bộ chứng từ</td>
                <td>Diễn giải</td>
                <td>Trạng thái</td>


            </tr>
            </thead>
            <tbody>
            @php
                $tong_tat_ca_tnct = 0;
                $tong_tat_ca_thue_tam_trich = 0;
                $tong_tat_ca_thuc_nhan = 0;
                $hasSalary=false;
                $listStatusIsZero = [];
            @endphp
            @foreach($data as $d)
                @if($d->type!=1 || !$d->data)
                    @php
                        $month_year = ($d->status==1||$d->type==1)?$d->month.'/'.$d->year:date('n/Y',strtotime($d->created_at))
                    @endphp
                    @if($d->status !=1)
                    <tr @if($d->status!=1) class="text-danger" @endif>
                        <td class="text-center">
                            @if(strtotime($d->ngay_thanh_toan) && $d->ngay_thanh_toan!="0000-00-00 00:00:00")
                                {{date('d/m/Y',strtotime($d->ngay_thanh_toan))}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_tnct:$d->tong_tnct)}}</td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_thue_tam_trich:$d->thue_tam_trich)}}</td>
                        <td class="text-right">{{number_format($d->type == 1?$d->sum_thuc_nhan:$d->thuc_nhan)}}</td>
                        <td class="text-right">F-{{$d->order_id}}</td>
                        <td class="text-left">
                            {{$d->order->noi_dung}}
                        </td>
                        <td class="text-right">@if($d->status==1) <a href="javascript:void(0)" class=""
                                                                     title="Đã thanh toán">
                                <i class="fa fa-flag" aria-hidden="true"></i>
                            </a> @else <a href="javascript:void(0)" class="" title="Chờ thanh toán">
                                <i class="fa fa-flag" style="color: red " aria-hidden="true"></i>
                            </a> @endif</td>
                        @php
                            if($d->type==1){
                                $hasSalary = true;
                            }
                            if($d->status!=1){
                                $tong_tat_ca_tnct+=$d->tong_tnct;
                                $tong_tat_ca_thue_tam_trich +=$d->thue_tam_trich;
                                $tong_tat_ca_thuc_nhan +=$d->thuc_nhan;
                            }
                        @endphp
                    </tr>
                        @endif
                @endif
            @endforeach

            <tr class="text-danger">
                <td class="text-center">Tổng</td>
                <td class="text-right">
                    {{number_format($tong_tat_ca_tnct)}}
                </td>
                <td class="text-right">
                    {{number_format($tong_tat_ca_thue_tam_trich)}}
                </td>
                <td class="text-right">
                    {{number_format($tong_tat_ca_thuc_nhan)}}
                </td>
                <td colspan="3"></td>

            </tr>

            </tbody>
        </table>
    @endif
</div>