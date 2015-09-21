@extends('templates.default')

@section('content')
    <h1>Upload an Accounts File</h1>
    <br>

    <form method="POST" action="/governments/{!!$governmentId!!}/accounts/upload" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="chart" value="{!! $chartId !!}">

        <div class="form-group">
            {!!  Form::label('accounts', 'Accounts File') !!}
            {!!  Form::file('accounts') !!}

            <span class="error">{!!  $errors->first('fileerror')  !!}</span>
        </div>
        <br/>
        <div class="form-group">
            {!!  Form::submit('Upload', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
    <br>


    <p>The file must be a CSV file with three columns: </p>
    <ul>
        <li>Account code</li>
        <li>Account name</li>
        <li>Account type - one of:
            <ul>
                <li>revenue</li>
                <li>expense</li>
                <li>asset</li>
                <li>liability</li>
                <li>equity</li>
                <li>contra</li>
                <li>unknown</li>
            </ul>
        </li>
    </ul>


@stop