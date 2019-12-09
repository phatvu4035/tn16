@extends('layouts.app')

@section('content')
    <table style="width: 100%;margin: 0 50px;">
        <thead>
        <tr>
            <td>Tên</td>
            <td>Email</td>
            <td>Ngày tạo</td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $user)
            <tr>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection