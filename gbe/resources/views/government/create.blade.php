@extends('templates.default')

@section('content')

    <form method="POST" action="/system/governments" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <h1>New Government Organization</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
            <br/>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
