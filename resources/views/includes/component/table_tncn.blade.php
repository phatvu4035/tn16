<!-- Modal -->
<div class="modal fade show" id="infoEmployee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" style="max-width: 80%" role="document" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Chi tiết thu nhập</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<button type="button" class="btn btn-primary" style="display: none" id="btnModel" data-toggle="modal"
        data-target="#infoEmployee">Launch demo modal
</button>
<div class="table-responsive">
    <table class="table table-bordered">
        {{--    @php(dd($data))--}}
        <thead>
        <tr>
            @foreach($header as $key => $h)
                <th data-display="{{isset($h['display'])?$h['display']:true}}" data-id="{{$key}}" id="{{$key}}"
                    scope="col">{{$h['name']}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @if(isset($report))
            @if(!is_array($report))
                @php($report = $report->toArray())
            @endif
            @foreach($report as $r)
                <tr>
                    @foreach($header as $k=>$h)
                        @if($h['type']=='number_format')
                            <th data-id="{{$k}}" class="{{$h['class']}}">{{isset($r[$k])?number_format($r[$k]):0}}</th>
                        @else
                            <th data-id="{{$k}}" class="{{$h['class']}}">{{isset($r[$k])?($r[$k]):""}}</th>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        @endif
        @if(count($data)>0 || count($dataNotStatus)>0)
            @foreach($data as $d)
                <tr>
                    @foreach($header as $k=>$h)
                        @if($h['type']=='string')
                            @php($value = $d->$k)
                            @if(isset($h['option']['value']))
                                @if(is_array($h['option']['value']))
                                    @foreach($h['option']['value'] as $re)
                                        @if(isset($d[$re['table']]['deleted_at'])&&$d[$re['table']]['deleted_at'])
                                            @php($isDelete=true)
                                        @else
                                            @php($isDelete=false)
                                        @endif
                                        @if($d[$re['table']])
                                            @php($value = $d[$re['table']][$re['value']])
                                        @endif
                                    @endforeach
                                @else
                                    @if(isset($h['option']['conditions']) && is_array($h['option']['conditions']))
                                        {{--                                @php(dd($d[$h['option']['conditions'][0]]))--}}
                                        @if($h['option']['conditions'][1]=='=')
                                            @if($d[$h['option']['conditions'][0]] == $h['option']['conditions'][2])
                                                @php($value=$d[$h['option']['value']])
                                            @else
                                                @php($value='')
                                            @endif
                                        @endif
                                    @else
                                        @php($value=$d[$h['option']['value']])
                                    @endif

                                @endif

                            @endif

                            @if(isset($isDelete)&&$isDelete)
                                <td data-delete="true" data-id="{{$k}}" class="{{$h['class']}}">{{$value}}</td>
                            @else
                                <td data-delete="false" data-id="{{$k}}" class="{{$h['class']}}">{{$value}}</td>
                            @endif
                        @endif
                        @if($h['type']=='number_format')
                            <td data-id="{{$k}}" class="{{$h['class']}}">{{number_format($d->$k)}}</td>
                        @endif
                        @if($h['type']=='datetime')
                            @if(strtotime($d->$k) && $d->$k!="0000-00-00 00:00:00")
                                <td data-id="{{$k}}"
                                    class="{{$h['class']}}">{{date('d/m/Y',strtotime($d->$k))}}</td>
                            @else
                                <td data-id="{{$k}}" class="{{$h['class']}}"></td>
                            @endif

                        @endif
                        @if($h['type']=='option')
                            <td data-id="{{$k}}" class="{{$h['class']}}">{{$h['option'][$d->$k]}}</td>
                        @endif

                        @if($h['type']=='image')
                            <td data-id="{{$k}}" class="{{$h['class']}}"><img
                                        src="@isset($d->$k) {{ $d->$k }} @endisset">
                            </td>
                        @endif

                        @if( $h['type']=='relationship' )
                            @if( $h['relationship_type'] == 'one_to_many')
                                @foreach($k->{$h['relationship_name']} as $relation)
                                    <td data-id="{{$k}}"
                                        class="{{$h['class']}}"> @isset( $relation->{$h['desire_value']} ) {{ $relation->{$h['desire_value']} }} @endisset</td>
                                @endforeach
                            @endif
                            @if( $h['relationship_type'] == 'one_to_many_inverse')
                                <td data-id="{{$k}}"
                                    class="{{$h['class']}}">@isset( $d->{$h['relationship_name']}->{$h['desire_value']} ) {{ $d->{$h['relationship_name']}->{$h['desire_value']} }} @endisset</td>
                            @endif
                        @endif

                        {{--for list employee--}}
                        @if($h['type']=='flag')
                            @php($flag = false)
                            @if($d['is_salary']>0&&$d['is_status_salary']>0)
                                @php($flag= true)
                            @endif
                            @if(!$flag)
                                @if($d['is_salary']==0 && $d['is_status']>0)
                                    @php($flag= true)
                                @endif
                            @endif
                            <td class="{{$h['class']}}">
                                <a href="javascript:void(0)" class=""
                                   title="@if($flag)Đã qua kiểm tra, đã được ghi nhận bởi TCB và kế toán @else Đã được ghi nhận bởi TCB, đang trong quá trình kiểm tra giữa TCB và kế toán @endif">
                                    <i class="fa fa-flag"
                                       style="@if(!$flag)color: red @endif"
                                       aria-hidden="true"></i>
                                </a>
                            </td>
                        @endif
                        {{--end for list employee--}}
                        @if($h['type']=='ajax')
                            <td class="{{$h['class']}}">
                                @if(isset($h['action']['view_ajax_modal_temp_1']) )
                                    <a data-href="{{ route($h['action']['view_ajax_modal_temp_2']['temp'])}}"
                                       data-employee="{{$d->employee_code}}" data-pn="{{$d->phap_nhan}}"
                                       data-my={{$d->month_year}} data-is_additional_order="IsNotAddOrder"
                                               href="javascript:void(0)" class="" data-view="ajax"
                                       style="padding-right: 20px"
                                       title="Hiển thị nhanh com, thưởng">
                                        <i class="fa fa-table" style="color: blue" aria-hidden="true"></i>
                                    </a>
                                    @if($d->is_salary>0)
                                        <a data-href="{{ route($h['action']['view_ajax_modal_temp_1']['temp'])}}"
                                           data-employee="{{$d->employee_code}}" data-pn="{{$d->phap_nhan}}"
                                           data-my={{$d->month_year}}
                                                   href="javascript:void(0)" class="" data-view="ajax"
                                           title="Hiển thị chi tiết bảng lương">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                @endif
                            </td>
                        @endif
                        @if($h['type']=='action')
                            <td class="{{$h['class']}}" style="width: 110px;">

                                @if(isset($h['checkRole']['edit']) && Topica::can($h['checkRole']['edit']))
                                    @if(isset($h['action']['edit']) )

                                        <a href="{{ route($h['action']['edit'],$d->id) }}" class="btn btn-primary"
                                           title="Chỉnh sửa">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </a>

                                    @endif
                                @endif
                                @if(isset($h['checkRole']['delete']) && Topica::can($h['checkRole']['delete']))
                                    @if(isset($h['action']['delete']))

                                        @if( $d->getTable() !== 'users' || ($d->getTable() == 'users' && $d->id !== Auth::user()->id ) )
                                            <form action="{{route($h['action']['delete'],$d->id)}}" method="post"
                                                  style="display: inline-block" id="form-delete">
                                                {{ method_field('delete') }}
                                                {!! csrf_field() !!}
                                                <button class="btn btn-danger" title="Xóa" type="submit"><i
                                                            class="fa fa-trash-o"
                                                            aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif

                                    @endif
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{count($header)+1}}" style="text-align: center">Không có dữ liệu</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@if(count($dataIsAdd)>0)
    <div class="row mT-30">
        <div class="col-sm-12">
            <h3>Danh sách thu nhập bổ sung cuối năm</h3>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            {{--    @php(dd($data))--}}
            <thead>
            <tr>
                @foreach($header as $key => $h)
                    <th data-display="{{isset($h['display'])?$h['display']:true}}" data-id="{{$key}}" id="{{$key}}"
                        scope="col">{{$h['name']}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @if(isset($reportIsAdd))
                @if(!is_array($reportIsAdd))
                    @php($reportIsAdd = $reportIsAdd->toArray())
                @endif
                @foreach($reportIsAdd as $r)
                    <tr>
                        @foreach($header as $k=>$h)
                            @if($h['type']=='number_format')
                                <th data-id="{{$k}}"
                                    class="{{$h['class']}}">{{isset($r[$k])?number_format($r[$k]):0}}</th>
                            @else
                                <th data-id="{{$k}}" class="{{$h['class']}}">{{isset($r[$k])?($r[$k]):""}}</th>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @endif
            @if(count($dataIsAdd)>0 || count($dataIsAdd)>0)
                @foreach($dataIsAdd as $d)
                    <tr>
                        @foreach($header as $k=>$h)
                            @if($h['type']=='string')
                                @php($value = $d->$k)
                                @if(isset($h['option']['value']))
                                    @if(is_array($h['option']['value']))
                                        @foreach($h['option']['value'] as $re)
                                            @if(isset($d[$re['table']]['deleted_at'])&&$d[$re['table']]['deleted_at'])
                                                @php($isDelete=true)
                                            @else
                                                @php($isDelete=false)
                                            @endif
                                            @if($d[$re['table']])
                                                @php($value = $d[$re['table']][$re['value']])
                                            @endif
                                        @endforeach
                                    @else
                                        @if(isset($h['option']['conditions']) && is_array($h['option']['conditions']))
                                            {{--                                @php(dd($d[$h['option']['conditions'][0]]))--}}
                                            @if($h['option']['conditions'][1]=='=')
                                                @if($d[$h['option']['conditions'][0]] == $h['option']['conditions'][2])
                                                    @php($value=$d[$h['option']['value']])
                                                @else
                                                    @php($value='')
                                                @endif
                                            @endif
                                        @else
                                            @php($value=$d[$h['option']['value']])
                                        @endif

                                    @endif

                                @endif

                                @if(isset($isDelete)&&$isDelete)
                                    <td data-delete="true" data-id="{{$k}}" class="{{$h['class']}}">{{$value}}</td>
                                @else
                                    <td data-delete="false" data-id="{{$k}}" class="{{$h['class']}}">{{$value}}</td>
                                @endif
                            @endif
                            @if($h['type']=='number_format')
                                <td data-id="{{$k}}" class="{{$h['class']}}">{{number_format($d->$k)}}</td>
                            @endif
                            @if($h['type']=='datetime')
                                @if(strtotime($d->$k) && $d->$k!="0000-00-00 00:00:00")
                                    <td data-id="{{$k}}"
                                        class="{{$h['class']}}">{{date('d/m/Y',strtotime($d->$k))}}</td>
                                @else
                                    <td data-id="{{$k}}" class="{{$h['class']}}"></td>
                                @endif

                            @endif
                            @if($h['type']=='option')
                                <td data-id="{{$k}}" class="{{$h['class']}}">{{$h['option'][$d->$k]}}</td>
                            @endif

                            @if($h['type']=='image')
                                <td data-id="{{$k}}" class="{{$h['class']}}"><img
                                            src="@isset($d->$k) {{ $d->$k }} @endisset">
                                </td>
                            @endif

                            @if( $h['type']=='relationship' )
                                @if( $h['relationship_type'] == 'one_to_many')
                                    @foreach($k->{$h['relationship_name']} as $relation)
                                        <td data-id="{{$k}}"
                                            class="{{$h['class']}}"> @isset( $relation->{$h['desire_value']} ) {{ $relation->{$h['desire_value']} }} @endisset</td>
                                    @endforeach
                                @endif
                                @if( $h['relationship_type'] == 'one_to_many_inverse')
                                    <td data-id="{{$k}}"
                                        class="{{$h['class']}}">@isset( $d->{$h['relationship_name']}->{$h['desire_value']} ) {{ $d->{$h['relationship_name']}->{$h['desire_value']} }} @endisset</td>
                                @endif
                            @endif

                            {{--for list employee--}}
                            @if($h['type']=='flag')
                                @php($flag = false)
                                @if($d['is_salary']>0&&$d['is_status_salary']>0)
                                    @php($flag= true)
                                @endif
                                @if(!$flag)
                                    @if($d['is_salary']==0 && $d['is_status']>0)
                                        @php($flag= true)
                                    @endif
                                @endif
                                <td class="{{$h['class']}}">
                                    <a href="javascript:void(0)" class=""
                                       title="@if($flag)Đã qua kiểm tra, đã được ghi nhận bởi TCB và kế toán @else Đã được ghi nhận bởi TCB, đang trong quá trình kiểm tra giữa TCB và kế toán @endif">
                                        <i class="fa fa-flag"
                                           style="@if(!$flag)color: red @endif"
                                           aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endif
                            {{--end for list employee--}}
                            @if($h['type']=='ajax')
                                <td class="{{$h['class']}}">
                                    @if(isset($h['action']['view_ajax_modal_temp_1']) )
                                        <a data-href="{{ route($h['action']['view_ajax_modal_temp_2']['temp'])}}"
                                           data-employee="{{$d->employee_code}}" data-pn="{{$d->phap_nhan}}"
                                           data-my={{$d->month_year}} data-is_additional_order="IsAddOrder"
                                                   href="javascript:void(0)" class="" data-view="ajax"
                                           style="padding-right: 20px"
                                           title="Hiển thị nhanh com, thưởng">
                                            <i class="fa fa-table" style="color: blue" aria-hidden="true"></i>
                                        </a>
                                        @if($d->is_salary>0)
                                            <a data-href="{{ route($h['action']['view_ajax_modal_temp_1']['temp'])}}"
                                               data-employee="{{$d->employee_code}}" data-pn="{{$d->phap_nhan}}"
                                               data-my={{$d->month_year}}
                                                       href="javascript:void(0)" class="" data-view="ajax"
                                               title="Hiển thị chi tiết bảng lương">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                    @endif
                                </td>
                            @endif
                            @if($h['type']=='action')
                                <td class="{{$h['class']}}" style="width: 110px;">

                                    @if(isset($h['checkRole']['edit']) && Topica::can($h['checkRole']['edit']))
                                        @if(isset($h['action']['edit']) )

                                            <a href="{{ route($h['action']['edit'],$d->id) }}" class="btn btn-primary"
                                               title="Chỉnh sửa">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>

                                        @endif
                                    @endif
                                    @if(isset($h['checkRole']['delete']) && Topica::can($h['checkRole']['delete']))
                                        @if(isset($h['action']['delete']))

                                            @if( $d->getTable() !== 'users' || ($d->getTable() == 'users' && $d->id !== Auth::user()->id ) )
                                                <form action="{{route($h['action']['delete'],$d->id)}}" method="post"
                                                      style="display: inline-block" id="form-delete">
                                                    {{ method_field('delete') }}
                                                    {!! csrf_field() !!}
                                                    <button class="btn btn-danger" title="Xóa" type="submit"><i
                                                                class="fa fa-trash-o"
                                                                aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif

                                        @endif
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{count($header)+1}}" style="text-align: center">Không có dữ liệu</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endif