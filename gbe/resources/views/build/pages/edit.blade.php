@extends('templates.default')

@section('content')

    <form method="POST" action="/build/{!! $site->slug !!}/pages/{!! $page->id !!}" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="_method" value="PUT">

        <h1>Edit Page</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('title', 'Title: ')  !!}
            {!!  Form::text('title', $page->title, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('title')  !!}</span>
            <br>
        </div>
        <div class="form-group">
            {!!  Form::label('short_name', 'Short Name (for URLs - letters and numbers only): ')  !!}
            {!!  Form::text('short_name', $page->short_name, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('short_name')  !!}</span>
            <br>
        </div>
        <div class="form-group">
            {!!  Form::label('menu_name', 'Menu Display Name: ')  !!}
            {!!  Form::text('menu_name', $page->menu_name, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('short_name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            <label for="layout">Layout:</label>
            <select name="layout" >
                <option value="0" {!! $page->layout==null?'selected':' '!!}>--Default--</option>
                @foreach ($layouts as $layout)
                    <option value="{!! $layout->id !!}" {!! $page->layout==$layout->id?'selected':' '!!}>{!! $layout->name !!}</option>
                @endforeach
            </select>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', $page->description, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('description')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop