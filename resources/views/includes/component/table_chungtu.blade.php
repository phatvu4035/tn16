<!-- Modal -->
<div class="table-responsive">
    <table class="table table-bordered">
        {{--    @php(dd($data))--}}
        <thead>
        <tr>
            <th scope="col">#</th>
            @foreach($header as $key => $h)
                <th data-display="{{isset($h['display'])?$h['display']:true}}" data-id="{{$key}}" id="{{$key}}"
                    scope="col">{{$h['name']}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>

        @if(count($data)>0)
            @foreach($data as $d)
                <tr class="">
                    <th scope="row">{{$loop->index + 1}}</th>
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

                        @if($h['type']=='action')
                            <td class="{{$h['class']}}" style="width: 110px;">

                                @php($fixedTypes = getFixedTypeId())
                                
                                @if(isset($h['checkRole']['edit']) && Topica::can($h['checkRole']['edit']))
                                    @if(isset($h['action']['edit']) )

                                        @if( !in_array($d->id, $fixedTypes ) )
                                            <a href="{{ route($h['action']['edit'],$d->id) }}" class="btn btn-primary"
                                               title="Chỉnh sửa">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                    @endif
                                @endif
                                
                                @if(isset($h['checkRole']['delete']) && Topica::can($h['checkRole']['delete']))
                                    @if(isset($h['action']['delete']))

                                        @if( !in_array( $d->id, $fixedTypes) )
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
@if(isset($data)&& $data)
    @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator )
        <div>
            {{ $data->links('includes.component.pagination') }}
        </div>
    @endif
@endif