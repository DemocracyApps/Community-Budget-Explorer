@extends('templates.default')

@section('content')

<form method="POST" action="/build/{!! $site->slug !!}/cards" accept-charset="UTF-8" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
    <input type="hidden" name="cardSet" value="{!! $cardSet !!}">

    <h1>New Card</h1>

    <br>
    <div class="form-group">
        {!!  Form::label('title', 'Title: ')  !!}
        {!!  Form::text('title', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('title')  !!}</span>
        <br>
    </div>

    <br>
    <div class="form-group">
        {!!  Form::label('body', 'Body (you may use Markdown for formatting): ')  !!}
        {!!  Form::textarea('body', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('body')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::label('link', 'Link: ')  !!}
        {!!  Form::text('link', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('link')  !!}</span>
        <br>
    </div>


    <div class="form-group">
        {!! Form::label('image', 'Upload an image: ') !!}
        {!! Form::file('image') !!}
    </div>
    <br>


    <div class="form-group">
        {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
    </div>

</form>
@stop
