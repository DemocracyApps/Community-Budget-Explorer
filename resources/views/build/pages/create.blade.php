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
        {!!  Form::label('short_name', 'Short Name (for URLs - letters and numbers only): ')  !!}
        {!!  Form::text('short_name', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('short_name')  !!}</span>
        <br>
    </div>
    <div class="form-group">
        {!!  Form::label('menu_name', 'Menu Display Name: ')  !!}
        {!!  Form::text('menu_name', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('short_name')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        <label for="layout">Layout:</label>
        <select name="layout" >
            <option value="0">--Default--</option>
            @foreach ($layouts as $layout)
                <option value="{!! $layout->id !!}">{!! $layout->name !!}</option>
            @endforeach
        </select>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::label('description', 'Description: ')  !!}
        {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
        <br>
        <span class="error">{!!  $errors->first('description')  !!}</span>
        <br>
    </div>

    <div class="form-group">
        {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
    </div>

</form>
@stop
