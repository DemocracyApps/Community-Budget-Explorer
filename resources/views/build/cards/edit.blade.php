@extends('templates.default')

@section('content')

<form method="POST" action="/build/{!! $site->slug !!}/cards/{!! $card->id !!}" accept-charset="UTF-8" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="cardSet" value="{!! $cardSet !!}">

    <h1>Edit Card</h1>

    <br>
    <div class="form-group">
        {!!  Form::label('title', 'Title: ')  !!}
        {!!  Form::text('title', $card->title, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('title')  !!}</span>
        <br>
    </div>

    <br>
    <div class="form-group">
        {!!  Form::label('body', 'Body (you may use Markdown for formatting): ')  !!}
        {!!  Form::textarea('body', $card->body, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('body')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::label('link', 'Link: ')  !!}
        {!!  Form::text('link', $card->link, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('link')  !!}</span>
        <br>
    </div>


    <div class="form-group">
        {!! Form::label('image', 'Upload a new image: ') !!}
        {!! Form::file('image') !!}
    </div>
    <br>


    <div class="form-group">
        {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
    </div>

</form>
@stop
