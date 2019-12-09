<form action="{{route($action,$id)}}" method="post" style="display: inline-block">
    {{ method_field('delete') }}
    {!! csrf_field() !!}
    <button class="btn btn-danger" title="Xóa" type="submit" onclick="if(confirm('Are you sure?'))form.submit()"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
</form>