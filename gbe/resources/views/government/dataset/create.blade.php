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
            <span class="error">{!!  $errors->first('name')  !!}</span>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                {!!  Form::label('year', 'Year: ')  !!}
                {!!  Form::text('year', null, ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('year')  !!}</span>
            </div>
            <div class="col-md-6 form-group">
                {!!  Form::label('type', 'Type: ')  !!}
                {!!  Form::select('type', array('--'=>'--', 'actual'=>'Actual', 'budget'=>'Budget'), '--', ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('type')  !!}</span>
            </div>
        </div>
        <div class="form-group">
            {!!  Form::label('data', 'Data File') !!}
            <div>
                <p>The datafile must have 3+ columns: account type (revenue or expense); budget or actual value; 1 or more category types, in order
                    of deepening hierarchy; and account name. It's ok to have no categories, but the final
                    column must be an account name, which must be unique per account type, i.e., you cannot have 2 expense accounts with
                    the same name. The content of the header line is important for categories - the column headers for the category columns
                    will be used to name that category type. For example:</p>
                    <table class="table">
                        <tr>
                            <th>Type</th>
                            <th>Budget Amount</th>
                            <th>Fund</th>
                            <th>Department</th>
                            <th>Account Type</th>
                            <th>Account</th>
                        </tr>
                        <tr>
                            <td>EXPENSE</td>
                            <td>29628486</td>
                            <td>General Fund</td>
                            <td>Fire Operations</td>
                            <td>Salaries and Wages</td>
                            <td>Salaries-Full Time</td>
                        </tr>
                    </table>
            </div>
            {!!  Form::file('data', ['class'=>'btn btn-info']) !!}
            <span class="error">{!!  $errors->first('file')  !!}</span>
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
