@extends('templates.default')

@section ('buttons')
    <a href="/system/organizations" class="btn btn-info">Back to All Organizations</a>
@stop

@section('content')

<div class="row">
    <div class="col-xs-6">
        <h1>{!! $organization->name !!} </h1>
    </div>
    <div class="col-xs-6">
        <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/system/organizations/{!! $organization->id !!}/edit'">Edit</button>
    </div>
</div>

<div class="row">
    <table class="table">
        <tr>
            <th> ID </th>
            <th> Name </th>
            <th>  </th>
            <th>  </th>
        </tr>
        @foreach ($charts as $chart)
            <tr>
                <td> {!!  $chart->id !!} </td>
                <td> {!!  $chart->name  !!} </td>
                <td> <form method="GET" action="/system/organizations/{!! $organization->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                    </form>
                </td>
                <td> <form method="POST" action="/system/organizations/{!! $organization->id !!}" accept-charset="UTF-8" style="display:inline-block">
                        <input name="_method" type="hidden" value="DELETE">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@stop
