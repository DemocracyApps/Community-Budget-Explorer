@extends('templates.default')

@section('content')

    <div class="row">
        <div class="col-xs-6">
            <h1>{!! $organization->name !!} </h1>
        </div>
        <div class="col-xs-6">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/media/{!! $organization->id !!}/edit'">Edit</button>
        </div>
    </div>

    <div class="row">
        <p>{!! $organization->description !!} </p>
    </div>

@stop
