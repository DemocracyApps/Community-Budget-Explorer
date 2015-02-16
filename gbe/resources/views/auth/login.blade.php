@extends('templates.default')

@section('content')

  <h1>Log In</h1>
  <br>

  <form method="POST" action="/auth/login" >
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

    <div class="row">
      <div class="col-lg-5" style="margin-right: 30px; padding-right: 50px; border-right:thin solid #000000;">

        <div class="form-group">
          {!! Form::label('email', 'Email: ') !!}
          {!! Form::text('email', null, ['class' => 'form-control']) !!}
          <br>
          <span class="error">{!! $errors->first('email') !!}</span>
        </div>
        <div class="form-group">
          {!! Form::label('password', 'Password: ') !!}
          {!! Form::password('password', ['class' => 'form-control']) !!}
          <br>
          <span class="error">{!! $errors->first('password') !!}</span>
        </div>
        <br>
        <div class="form-group">
          <input name="PW" type="submit" style="width:200px;" class='btn btn-primary' value="Email Sign In">
        </div>
      </div>

      <div class="col-lg-2"></div>
      <div class="col-lg-5">
        <p>
          <input name="FB" type="submit" style="width:200px;" class='btn btn-primary' value="Facebook Sign In">
        </p>
        <br>
        <p>
          <input name="TW" type="submit" style="width:200px;" class='btn btn-primary' disabled value="Twitter Sign In">
        </p>

      </div>

    </div>
  </form>
  <br>
  <br>

  <hr>

  <p> No account? <a href={!!url('auth/register')!!}>Sign up here</a></p>
@stop
