@extends('templates.default')

@section('content')
    @include('system.tabs', ['page'=>'layouts'])

    <div class="row">
        <div class="col-xs-6">
            <h1>Layouts</h1>
        </div>
        <div class="col-xs-6">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/system/layouts/create'">New</button>
        </div>
    </div>

    <table class="table">
        <tr>
            <th> ID </th>
            <th> Name </th>
            <th> Description</th>
            <th> Public?</th>
            <th>  </th>
        </tr>
        @foreach ($layouts as $layout)
            <tr>
                <td> {!!  $layout->id !!} </td>
                <td> {!!  $layout->name  !!} </td>
                <td> {!! $layout->description !!}</td>
                <td> {!! $layout->public?'Yes':'No'!!}</td>
                <td> <form method="GET" action="/system/layouts/{!! $layout->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@stop
