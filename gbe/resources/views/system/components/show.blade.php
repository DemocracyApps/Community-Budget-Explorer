@extends('templates.default')

@section('content')
    <h1>{!! $component->name !!}</h1>

    <p>{!! $component->description !!}</p>
    <br>
    <h3>Data Specification</h3>
    <pre>
        <code>
            {!! json_encode($component->getProperty('data'), JSON_PRETTY_PRINT) !!}
        </code>
      </pre>
@stop