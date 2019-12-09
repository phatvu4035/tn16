<table>
    <thead>
    @foreach($key as $k=>$v)
        @if($loop->first)
            <tr>
                <td>MNV</td>
                @foreach($v['thuong_nv'] as $k1=>$v1)

                    <th>{{$k1}}</th>

                @endforeach

            </tr>
        @endif
    @endforeach

    </thead>
    <tbody>
    @foreach($key as $k=>$v)
        <tr>
            <td>{{$v['employee_code']}}</td>
            @foreach($v['thuong_nv'] as $k2=>$v2)
                <td>{{$v2}}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>