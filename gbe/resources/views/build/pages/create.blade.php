@extends('templates.default')

@section('content')

<form method="POST" action="/build/{!! $site->slug !!}/pages" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

    <h1>New Page</h1>

    <br>
    <div class="form-group">
        {!!  Form::label('title', 'Title: ')  !!}
        {!!  Form::text('title', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('title')  !!}</span>
        <br>
    </div>
    <div class="form-group">
        {!!  Form::label('short_name', 'Short Name (for menus): ')  !!}
        {!!  Form::text('short_name', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('short_name')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
    </div>

</form>
@stop
