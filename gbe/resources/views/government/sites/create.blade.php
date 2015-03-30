@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/sites" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <h1>New Site</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('slug', 'Slug: ')  !!}
            {!!  Form::text('slug', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('slug')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
