@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('media.tabs', ['page'=>'users'])


    <form method="POST" action="/media/{!!$organization->id !!}/users" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <h1>New {!! $organization->name !!} User</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('email', 'Email Address: ')  !!}
            {!!  Form::text('email', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="text-danger bg-danger">{!!  $errors->first('email')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="text-danger bg-danger">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            <select name='access' class="form-control">
                <option value="0" selected>No privileges</option>
                <option value="9">Administrator</option>
            </select>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
            <a href="/media/{!! $organization->id !!}/users" class="btn btn-danger">Cancel</a>
        </div>

    </form>

@stop