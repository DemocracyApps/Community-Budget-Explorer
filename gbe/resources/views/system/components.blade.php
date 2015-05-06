@extends('templates.default')

@section('content')
    @include('system.tabs', ['page'=>'components'])

    <div class="row">
        <div class="col-xs-6">
            <h1>Display Components</h1>
        </div>
        <div class="col-xs-6">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/system/components/create'">New</button>
        </div>
    </div>

    <table class="table">
        <tr>
            <th> ID </th>
            <th> Name </th>
            <th> Description</th>
            <th> </th>
            <th>  </th>
        </tr>
        @foreach ($components as $component)
            <tr>
                <td> {!!  $component->id !!} </td>
                <td> <a href="/system/components/{!! $component->id !!}"> {!!  $component->name  !!} </a> </td>
                <td> {!! $component->description !!}</td>
                <td> <form method="GET" action="/system/components/{!! $component->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                    </form>
                </td>
                <td> <form method="POST" action="/system/components/{!! $component->id !!}" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <input name="_method" type="hidden" value="DELETE">
                        <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@stop
