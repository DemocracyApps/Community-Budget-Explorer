@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/data/{!! $datasource['id'] !!}" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="organization" value="{!! $organization->id !!}">
        <input type="hidden" name="datasource" value="{!! $datasource['id'] !!}">
        <input name="_method" type="hidden" value="PUT">

        <h1>Edit Data Source</h1>

        <br>
        <div class="row">
            <div class="col-md-6 form-group">
                {!!  Form::label('name', 'Name: ')  !!}
                {!!  Form::text('name', $datasource['name'], ['class' => 'form-control'])  !!}
                <span class="error">{!!  $errors->first('name')  !!}</span>
            </div>
            <div class="col-md-6 form-group">
                <label for="type">Source Type: </label>
                <!-- Using laravel form-builder to retain selection on error -->
                {!! Form::select('type', array('file'=>'File Upload', 'api'=>'API'), $datasource['sourceType'], 
                                 ['id'=>'sourcetype', 'onchange'=>'setType()', 'class' => 'form-control']) !!}
                <span class="error">{!!  $errors->first('type')  !!}</span>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <input name="description" type="text" class="form-control" value="{!! $datasource['description'] !!}">
                <span class="error">{!!  $errors->first('description')  !!}</span>
            </div>
        </div>
        <div class="row" id="fileparams" style="display:none;">
            <div class="col-md-6">
                <label for="data-format">Data Format</label>
                <select name="data-format" class="form-control">
                    <option value="simple-budget">Simple Budget</option>
                    <option value="simple-project">Simple Project</option>
                </select>
            </div>        
        </div>
        <div class="row" id="apiparams" style="display:none;">
            <div class="col-md-6">
                <label for="api-format">API Format</label>
                <select name="api-format" class="form-control">
                    <option {!! ($datasource['apiFormat'] == 'json')?'selected':'' !!} value="json">JSON</option>
                    <option {!! ($datasource['apiFormat'] == 'csv')?'selected':'' !!} value="csv">CSV</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="endpoint">API Endpoint</label>
                <input name="endpoint" type="text" class="form-control" value="{!! $datasource['endpoint'] !!}">
                <span class="error">{!!  $errors->first('endpoint')  !!}</span>
            </div>
            <div class="col-md-6">
                <label for="data-format">Data Format</label>
                <select name="data-format" class="form-control">
                    <option {!! ($datasource['dataFormat'] == 'simple-budget')?'selected':'' !!} value="simple-budget">Simple Budget</option>
                    <option {!! ($datasource['dataFormat'] == 'simple-project')?'selected':'' !!} value="simple-project">Simple Project</option>
                </select>
            </div>
            <div class="col-md-6">
                <!-- Using laravel form-builder to retain selection on error -->
                <label for="frequency">Frequency</label>
                {!! Form::select('frequency', 
                                 array('ondemand'=>'On-Demand', 'day'=>'Daily', 'hour'=>'Hourly', 'week'=>'Weekly'),
                                 $datasource['frequency'], ['class' => 'form-control']) !!}
            </div>
        </div>
        <br>
        <div class="row form-group">
            <div class="col-md-12">
                {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
            </div>
        </div>

    </form>
@stop

@section('scripts')

    <script>
        $(function() {
            setType();
        });

        function setType() {
            var value = $("#sourcetype").val();
            console.log("Here's the value: " + value);
            if (value == 'api') {
                document.getElementById("apiparams").style.display = "block";
                document.getElementById("fileparams").style.display = "none";
            }
            else if (value == 'file') {
                document.getElementById("apiparams").style.display = "none ";
                document.getElementById("fileparams").style.display = "block";
            }
        }
    </script>
@stop
