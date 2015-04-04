@extends('templates.default')

@section('content')

<form method="POST" action="/build/{!! $site->slug !!}/pages/{!!$page->id!!}/rows" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

    <h1>New Row</h1>

    <br>
    <div class="form-group">
        {!!  Form::label('title', 'Give the row a title: ')  !!}
        {!!  Form::text('title', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('title')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
    </div>

</form>
@stop
