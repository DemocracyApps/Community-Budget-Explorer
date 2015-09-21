@extends('templates.default')

@section('content')
    <h1>Edit Layout</h1>
    <form method="POST" action="/system/layouts/{!! $layout->id !!}" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input name="_method" type="hidden" value="PUT">

        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', $layout->name, ['class' => 'form-control'])  !!}
            <br/>
            <span class="error">{!!  $errors->first('name')  !!}</span>
        </div>
        <br/>
        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', $layout->description, ['class' => 'form-control'])  !!}
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
            {!!  Form::submit('Update Layout', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop