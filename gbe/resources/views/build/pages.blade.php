@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $site->name !!} </h1>
    </div>
    @include('build.tabs', ['page'=>'pages'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/build/{!! $site->slug !!}/edit'">Edit {!!$site->slug!!}</button>
        </div>
    </div>

    <div class="row">
        <p>This page will have pages and menus </p>
    </div>

@stop
