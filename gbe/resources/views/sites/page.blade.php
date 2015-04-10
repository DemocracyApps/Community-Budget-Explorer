@extends('templates.sites.default')

@section('content')
    <div id="app">

    </div>

    <div id="tasks">

    </div>
@stop

@section('scripts')

    <?php
    JavaScript::put([
        'ajaxPath' => Util::ajaxPath('sites', 'page'),
        'site' => $site,
        'pages'=> $pages,
        'page' => $page,
        'layout'=>$layout,
        'components'=>$components
    ]);
    ?>

    <script src="/js/bundle.js"></script>
@stop
