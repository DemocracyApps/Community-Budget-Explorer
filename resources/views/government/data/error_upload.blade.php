@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <p class="error text-danger bg-danger">
                <b>An error occurred while contacting the dataserver. Please contact the server administrator.</b>
                <br>
                <b>Error message: </b> &nbsp; {!! $message !!}
            </p>
                <br>
        </div>
        <div class="col-md-6">
            <button style="float:right; position:relative; right:50px; bottom:5px;" class="btn btn-primary btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}/data'">Continue</button>
        </div>
    </div>
@stop
