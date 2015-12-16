@extends('templates.default')

@section('content')

    <form method="POST" action="/governments/{!!$organization->id!!}/data/upload" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="organization" value="{!! $organization->id !!}">
        <input type="hidden" name="datasource" value="{!! $datasource !!}">

        <h1>Upload A Data File</h1>

        <br>
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="format">Format: </label>
                <select id="format" name="format" class="form-control" onchange="setFormat()">
                    <option value="simple-budget" selected>Simple Budget</option>
                    <option value="simple-project">Simple Project</option>
                </select>
                <span class="error">{!!  $errors->first('format')  !!}</span>
            </div>
        </div>

        <br>

        <div id="simplebudget" style="display:none;">
            <div class="col-md-12 form-group">
                <b>Documentation:</b><br>
                <p>The simple budget format consists of one or more category columns followed by
                    one or more data columns. The base name plus the column header will be used to label
                    each dataset.
                </p>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="name">Base Name</label>
                    <input type="text" name="name" class="form-control">
                    <span class="error">{!! $errors->first('name') !!}</span>
                </div>
                <div class="col-md-2 form-group"></div>
                <div class="col-md-4 form-group">
                    <label for="type">Type: </label>
                    <select name="type" class="form-control">
                        <option value="Expense" selected>Expense</option>
                        <option value="Revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="categories">Number of Categories</label>
                    <input type="text" value='' name="categories" class="form-control">
                    <span class="error">{!!  $errors->first('categories')  !!}</span>
                </div>
                <div class="col-md-4 form-group">
                    <label for="year_count">Number of Years</label>
                    <input type="text" name="year_count" class="form-control">
                    <span class="error">{!!  $errors->first('year_count')  !!}</span>
                </div>
                <div class="col-md-4 form-group">
                    <label for="year">Starting Year</label>
                    <input type="text" name="year" class="form-control">
                    <span class="error">{!!  $errors->first('year')  !!}</span>
                </div>
            </div>
            <div id='simpleproject' style="display:none;">
                ...
            </div>
        </div>

        <br>
        <div class="form-group">
            {!!  Form::label('data', 'Data File') !!}
            {!!  Form::file('data', ['class'=>'btn btn-info']) !!}
            <span class="error">{!!  $errors->first('file')  !!}</span>
        </div>
        <br>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop

@section('scripts')

    <script>
        $(function() {
            setFormat();
        });

        function setFormat() {
            var value = $("#format").val();
            var div1 = document.getElementById("simplebudget");
            var div2 = document.getElementById("simpleproject");
            if (value == 'simplebudget') {
                div1.style.display = 'block';
                div2.style.display = 'none';
            }
            else {
                div1.style.display = 'none';
                div2.style.display = 'block';
            }
        }
    </script>
@stop
