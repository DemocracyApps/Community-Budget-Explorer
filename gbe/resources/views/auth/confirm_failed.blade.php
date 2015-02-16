@extends('templates.default')

@section('content')
    <h1>We're Sorry</h1>

    <p>Your confirmation code is not valid.</p>
    <br>

    <p><a href="{!! url('/') !!}" class='btn btn-primary'>Continue</a></p>


@stop