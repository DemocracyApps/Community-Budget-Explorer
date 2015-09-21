@extends('templates.default')

@section('content')
    <h1>Thank You!</h1>

    <p>You have been successfully confirmed.</p>
    <br>

    <p><a href="{!! url('/') !!}" class='btn btn-primary'>Continue</a></p>


@stop