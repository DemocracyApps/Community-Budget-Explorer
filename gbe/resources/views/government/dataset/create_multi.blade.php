@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/datasets" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="organization" value="{!! $organization->id !!}">
        <input type="hidden" name="chart" value="{!! $chart->id !!}">
        <input type="hidden" name="multi" value="true">

        <h1>Upload Multiple New Datasets</h1>
        <p>Note that column numbering starts from 1. </p>
        <br>
        <div class="form-group">
            {!!  Form::label('name', 'Dataset Basename (Optional - prepended to data column headers): ')  !!}
            {!!  Form::text('name', null, ['class' => 'form-control'])  !!}
            <span class="error">{!!  $errors->first('name')  !!}</span>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                {!!  Form::label('year', 'Starting Year: ')  !!}
                {!!  Form::text('year', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('year')  !!}</span>
            </div>
            <div class="col-md-4 form-group">
                {!!  Form::label('year_count', 'Number of Years: ')  !!}
                {!!  Form::text('year_count', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('year_count')  !!}</span>
            </div>
            <div class="col-md-4 form-group">
                {!!  Form::label('year_column', 'Starting Year Column: ')  !!}
                {!!  Form::text('year_column', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('year_column')  !!}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                {!!  Form::label('categories', 'Number of Categories: ')  !!}
                {!!  Form::text('categories', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('categories')  !!}</span>
            </div>
            <div class="col-md-4 form-group">
                {!!  Form::label('categories_column', 'Starting Category Column: ')  !!}
                {!!  Form::text('categories_column', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('categories_column')  !!}</span>
            </div>
        </div>

        <div class="form-group">
            {!!  Form::label('data', 'Data File') !!}
            <div>
                <p>The datafile must have 3+ columns. The first column must have the account type (revenue or expense). The
                    file should also contain 1 or more columns of budget or actual values and 1 or more columns of category types, in order
                    of deepening hierarchy plus an account name. It's ok to have no categories, but the final
                    column must be an account name, which must be unique per account type, i.e., you cannot have 2 expense accounts with
                    the same name. Column headers are used to name the categories and the datasets.</p>
            </div>
            {!!  Form::file('data', ['class'=>'btn btn-info']) !!}
            <span class="error">{!!  $errors->first('file')  !!}</span>
        </div>
        <br>
        <div class="form-group">
            {!!  Form::label('description', 'Description (Optional): ')  !!}
            {!!  Form::textarea('description', null, ['class' => 'form-control'])  !!}
            <span class="error">{!!  $errors->first('description')  !!}</span>
        </div>


        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
