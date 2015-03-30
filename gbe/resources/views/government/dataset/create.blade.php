@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/datasets" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="organization" value="{!! $organization->id !!}">
        <input type="hidden" name="chart" value="{!! $chart->id !!}">

        <h1>Upload A New Dataset</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('name', 'Dataset Name: ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6 form-group">
                {!!  Form::label('year', 'Year: ')  !!}
                {!!  Form::text('year', null, ['class' => 'form-control'])  !!}
                <br>
                <span class="error">{!!  $errors->first('year')  !!}</span>
                <br>
            </div>
            <div class="col-md-6 form-group">
                {!!  Form::label('type', 'Type: ')  !!}
                {!!  Form::select('type', array('--'=>'--', 'actual'=>'Actual', 'budget'=>'Budget'), '--', ['class' => 'form-control'])  !!}
                <br>
                <span class="error">{!!  $errors->first('type')  !!}</span>
                <br>
            </div>
        </div>
        <div class="form-group">
            {!!  Form::label('data', 'Data File') !!}
            {!!  Form::file('data') !!}
            <span class="error">{!!  $errors->first('file')  !!}</span>
        </div>
        <br>
        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('description')  !!}</span>
            <br>
        </div>
        <br>



        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
