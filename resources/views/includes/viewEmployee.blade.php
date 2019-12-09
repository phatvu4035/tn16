<div class="container-fluid">
    {{--@php(dd($employee))--}}
    @if($employee)
        @foreach($employee as $k=>$e)
            <div class="row">
                <div class="col-md-12"><h5 style="border: 1px solid #ddd;padding: 10px">{!! $k !!}</h5></div>
            </div>
            <div class="row" style="padding: 10px">
                @if(count($e)==1)
                    @foreach($e as $k1=>$e1)
                        <div class="col-md-12">
                            @if($k1=="Mã NV HR 20" || !is_numeric($e1))
                                <label for=""><b>{{$k1}}</b>: <span>{{$e1}}</span></label>
                            @else
                                <label for=""><b>{{$k1}}</b>: <span>{{number_format($e1)}}</span></label>
                            @endif
                        </div>
                    @endforeach
                @else
                    @foreach($e as $k1=>$e1)
                        <div class="col-md-6">
                            @if($k1=="Mã NV HR 20" || !is_numeric($e1))
                                <label for=""><b>{{$k1}}</b>: <span>{{$e1}}</span></label>
                            @else
                                <label for=""><b>{{$k1}}</b>: <span>{{number_format($e1)}}</span></label>
                            @endif
                        </div>
                    @endforeach
                @endif

            </div>
        @endforeach
    @endif
</div>