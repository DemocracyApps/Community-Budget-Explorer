@extends('templates.sites.default')

@section('content')
    <div id="app">

    </div>


@stop

@section('scripts')

    <script src="/js/react-0.13.1/build/react-with-addons.js"></script>
    <script src="/js/react-0.13.1/build/JSXTransformer.js"></script>
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

    <script type="text/jsx;harmony=true" src="/js/components/TaskList.js"></script>
    <script type="text/jsx;harmony=true" src="/js/components/TaskApp.js"></script>
    <script type="text/jsx;harmony=true" src="/js/components/SimpleCard.js"></script>
    <script type="text/jsx;harmony=true" src="/js/components/BootstrapLayout.js"></script>


    <script type="text/jsx;harmony=true">
        var componentsMap = {};
        componentsMap['SimpleCard'] = SimpleCard;
        componentsMap['Other'] = TaskApp;
        var props = {layout:GBEVars.layout.specification, components:GBEVars.components, componentsMap:componentsMap}
        var layout = React.render(<BootstrapLayout {...props}/>, document.getElementById('app'));
    </script>
@stop
