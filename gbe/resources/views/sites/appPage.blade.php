@extends('templates.sites.default')

@section('head')

    <script type="text/javascript" src="/js/lib/mustache/mustache.js"></script>

<!--
<link rel="stylesheet" type="text/css" href="/css/avb/page.css">
-->
<link rel="stylesheet" type="text/css" href="/css/avb/global.css">

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
        $path = base_path() . '/resources/views/sites/treemap_templates.php';
        require_once($path);
    ?>

@stop
