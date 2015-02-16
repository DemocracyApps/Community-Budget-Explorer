@extends('layouts.default')

@section('content')
    <h1>We're Sorry</h1>

    <p>Posting is not allowed until you have confirmed your account.</p>
    <br>

    <p><a href="{{url('/confirm/resend')}}" class='btn btn-primary'>Click here to re-send confirmation email</a></p>


@stop