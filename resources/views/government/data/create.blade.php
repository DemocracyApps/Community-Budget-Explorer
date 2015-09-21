@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/data" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="organization" value="{!! $organization->id !!}">

        <h1>Create A New Data Source</h1>

        <br>
        <div class="row">
            <div class="col-md-6 form-group">
                {!!  Form::label('name', 'Name: ')  !!}
                {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('name')  !!}</span>
            </div>
            <div class="col-md-6 form-group">
                {!!  Form::label('type', 'Source Type: ')  !!}
                {!!  Form::select('type', array('file'=>'File Upload', 'api'=>'API'), '--', ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('type')  !!}</span>
            </div>
        </div>
        <br>
        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
            <span class="error">{!!  $errors->first('description')  !!}</span>
        </div>


        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
