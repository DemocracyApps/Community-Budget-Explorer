@extends('templates.sites.default')

@section('content')
    <div id="app">

    </div>
    <div     id="test">
        Yuppie.
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
            'layout'=>$layout
    ]);
    ?>

    <script type="text/jsx;harmony=true" src="/js/components/TaskList.js"></script>
    <script type="text/jsx;harmony=true" src="/js/components/TaskApp.js"></script>
    <script type="text/jsx;harmony=true" src="/js/components/BootstrapLayout.js"></script>

    <script type="text/jsx;harmony=true">
        var layout = React.render(<BootstrapLayout />, document.getElementById('app'));
        layout.setState({
            layout: GBEVars.layout
        });
        React.render(<TaskApp />, document.getElementById('test'));
    </script>
@stop
