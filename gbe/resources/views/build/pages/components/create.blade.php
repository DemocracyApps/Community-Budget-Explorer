@extends('templates.default')

@section('content')

    <form method="POST" action="/build/{!! $site->slug !!}/pages/{!!$page->id!!}/components" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <h1>Add a New Compponent</h1>

        <div class="form-group">
            <label for="component">Layout:</label>
            <select name="component" >
                @foreach ($components as $component)
                    <option value="{!! $component->id !!}">{!! $component->name !!}</option>
                @endforeach
            </select>
            <br>
        </div>
        <hr>
        <p>You can configure this component from the next screen.</p>
        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop