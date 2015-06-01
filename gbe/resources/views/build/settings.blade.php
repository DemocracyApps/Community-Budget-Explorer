@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $site->name !!} </h1>
    </div>
    @include('build.tabs', ['page'=>'settings'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/build/{!! $site->slug !!}/edit'">Edit {!!$site->slug!!}</button>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">
            <p><b>Site slug: </b> </p>
        </div>
        <div class="col-xs-9">
            <p>{!! $site->slug !!}</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">
            <p><b>Site Title: </b> </p>
        </div>
        <div class="col-xs-9">
            <p>{!! $site->name !!}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">
            <p><b>Category Map File: </b> </p>
        </div>
        <div class="col-xs-9">
            <p>{!! $site->getProperty('map') !!}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">
            <p><b>Site Is Live: </b> </p>
        </div>
        <div class="col-xs-9">
            <p>{!! $site->live?"Yes":"No" !!}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">
            <p><b>Scripts: </b> </p>
        </div>
        <div class="col-xs-9">
            <pre>{!! htmlspecialchars($site->scripts) !!}</pre>
        </div>
    </div>

@stop
