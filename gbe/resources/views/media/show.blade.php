@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('media.tabs', ['page'=>'organization'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/media/{!! $organization->id !!}/edit'">Edit {!!$organization->name!!}</button>
        </div>
    </div>

    <div class="row">
        <p>{!! $organization->description !!} </p>
    </div>

@stop
