@extends('templates.default')

@section('content')
    <h1>Register Component</h1>
    <form method="POST" action="/system/components" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <br/>
            <span class="error">{!!  $errors->first('name')  !!}</span>
        </div>
        <br/>
        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
            <br/>
        </div>
        <div class="form-group">
            {!!  Form::label('specification', 'Specification') !!}
            {!!  Form::file('specification') !!}
            <br/>
            <span class="error">{!!  $errors->first('fileerror')  !!}</span>
        </div>
        <br/>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop