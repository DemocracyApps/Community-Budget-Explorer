@extends('templates.sites.default')

@section('head')
    <link rel="stylesheet"
@stop
@section('content')

    <div id="app">

    </div>
@stop

@section('scripts')
    <?php
    JavaScript::put([
            'site' => $site,
            'pages'=> $pages,
            'data' => $data
    ]);
    ?>
@stop
