@extends('templates.default')

@section('content')
    <h1>Upload Values for Category {!! $category->name !!} </h1>
    <br>

    <form method="POST" action="/governments/{!!$governmentId!!}/accountcategories/upload" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="category" value="{!! $category->id !!}">

        <div class="form-group">
            {!!  Form::label('categories', 'Category Values File') !!}
            {!!  Form::file('categories') !!}

            <span class="error">{!!  $errors->first('fileerror')  !!}</span>
        </div>
        <br/>
        <div class="form-group">
            {!!  Form::submit('Upload', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>

@stop