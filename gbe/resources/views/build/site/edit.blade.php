@extends('templates.default')

@section('content')

    <form method="POST" action="/build/{!! $site->slug !!}" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input name="_method" type="hidden" value="PUT">

        <h1>Edit Site Information</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', $site->name, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('map', 'Category Mapping File (enter name of file in /public/data/maps): ')  !!}
            {!!  Form::text('map', $site->getProperty('map'), ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('map')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
