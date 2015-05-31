@extends('templates.default')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <h1>{!! $page->title !!} </h1>
            <a style="float:right; position:relative; bottom:25px;"
                    class="btn btn-success btn-sm"
                    href='/build/{!! $site->slug !!}/pages'>
                Back To All Pages</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a style="float:right; position:relative; right:50px; bottom:-20px;"
                    class="btn btn-success btn-sm"
                    href="/build/{!! $site->slug !!}/pages/{!! $page->id !!}/edit">
                Edit</a>
        </div>
        <div class="col-xs-6">
            <a href="/sites/{!!$site->slug!!}/{!!$page->short_name!!}">Link to Public Page</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4">
            <h2>Short Name</h2>
            <p> {!! $page->short_name !!} </p>
        </div>
        <div class="col-xs-1"></div>
        <div class="col-xs-6">
            <h2>Description</h2>
            <p>{!! $page->description !!} </p>
        </div>
    </div>
    <br>
    <div class="row">
        <h2>Components</h2>
        <table class="table">
            <tr>
                <th>Id</th><th>Name</th><th>Target</th><th></th>
            </tr>
            @foreach($pageComponents as $pc)
                <tr>
                    <td>{!! $pc->id !!}</td>
                    <td>{!! $components[$pc->component]->name !!}</td>
                    <td>
                        <select id="select_target_{!! $pc->id !!}"
                                onchange="return changeTarget({!!$pc->id!!})">
                            <option {!! $pc->target==null?'selected':' ' !!} value="--">--</option>
                            @foreach($targets as $target)
                                <option {!! $pc->target==$target?'selected':' ' !!} value="{!!$target!!}">{!!$target!!}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><a href="/build/{!! $site->slug !!}/pages/{!! $page->id !!}/components/{!! $pc->id !!}/edit">Configure</a></td>
                </tr>
            @endforeach
        </table>
        <a class="btn btn-primary" href="/build/{!! $site->slug !!}/pages/{!! $page->id !!}/components/create">New Component</a>
    </div>
    <div class="row">

        <h2>Layout</h2>
        <p>{!! $layout->name!!}</p>
        <br>
        <div class="col-md-1"></div>

        <div class="col-md-6">
            @foreach ($layout->specification['rows'] as $row)
                <div class="row">
                    @foreach ($row['columns'] as $column)
                        <div style="min-height:200px; border: 1px solid black;" class="{!! $column['class'] !!}">
                            <b>{!! $column['id'] !!}</b><br><br>
                            <div id="block_{!! $column['id'] !!}" style="padding-bottom: 1000px;margin-bottom: -1000px">
                                <p>Placeholder</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>

        <div class="col-md-5"></div>

    </div>

@stop

@section('scripts')
    <?php
    JavaScript::put([
        'ajaxPath' => Util::ajaxPath('build', 'pages'),
        'site' => $site,
        'page' => $page->id,
        'pageComponents'=>$pageComponents,
        'components' => $components,
        'layout'=>$layout
    ]);
    ?>

    <script>
        var blocks = new Array();
        $(function() {
            setupLayoutComponents();
            changeTarget(null);
        });

        function changeTarget(pcId)
        {
            blocks.map(function (current) {
                $("#block_"+current).empty();
            });
            var val = null;
            if (pcId != null) {
                val = $("#select_target_" + pcId).val();
                var source =GBEVars.ajaxPath + "/changeComponentTarget?pc="+pcId+"&target="+val;
                $.get( source, function( r ) {
                }).done(function(r) {
                    $("#flash").text(r.message);
                }).fail(function(r) {
                    $("#flash").text("Error saving target information: "+r.responseJSON.error.message);
                });
            }
            for (var i=0; i<GBEVars.pageComponents.length; ++i) {
                var pc = GBEVars.pageComponents[i];
                if (pcId != null && pc.id == pcId) {
                    pc.target = val;
                }
                if (pc.target != null) {
                    var block = $("#block_" + pc.target);
                    block.append("<p>" + GBEVars.components[pc.component].name + " </p>");
                }
            }
        }
        function publish(id) {
        }

        function setupLayoutComponents ()
        {
            GBEVars.layout.specification.rows.map(function (current, index) {
                current.columns.map(function(current, index) {
                    blocks.push(current.id);
                });
            });
        }

    </script>
@stop
