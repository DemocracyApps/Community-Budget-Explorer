@extends('templates.default')

@section('content')
  <h1>Sign up for the Community Narratives Platform</h1>
  
  <p>By signing up here you have reviewed and understand and agree to abide by the terms of the <a href="#"> membership agreement</a>.
    Click <a href="#">here</a> to download a PDF of the terms to print or save.</p>
  <br>
  
  <form method="POST" action="/auth/register" >
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

    <div class="row">
      <div class="col-lg-5" style="margin-right: 30px; padding-right: 50px; border-right:thin solid #000000;">
        <div class="form-group">
          {!! Form::label('name', 'Name: ') !!}
          {!! Form::text('name', null, ['class' => 'form-control']) !!}
          <br>
          <span class="error">{!! $errors->first('name') !!}</span>
        </div>
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
          <input name="PW" type="submit" style="width:200px;" class='btn btn-primary' value="Sign Up With Email">
        </div>
      </div>
    
      <div class="col-lg-2"></div>
    
      <div class="col-lg-5">
        <p>
          <input name="FB" type="submit" style="width:200px;" class='btn btn-primary' value="Sign Up With Facebook">
        </p>
        <br>
        <p>
          <input name="TW" type="submit" style="width:200px;" class='btn btn-primary' disabled value="Sign Up With Twitter">
        </p>
    
      </div>
    
    </div>
    
  </form>
  
@stop
