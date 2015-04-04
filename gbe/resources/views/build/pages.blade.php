@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $site->name !!} </h1>
    </div>
    @include('build.tabs', ['page'=>'pages'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-primary btn-sm" onclick="window.location.href='/build/{!! $site->slug !!}/pages/create'">New Page</button>
        </div>
    </div>

    <div class="row">
        <table id="sort" class="grid table" title="All Pages">
            <thead>
                <tr>
                    <th>Show In Menu?</th><th>Page Title</th><th>Page ShortName</th><th>Page Description</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pages as $page)
                <tr class="page-row" id="row_{!!$page->id!!}">
                    @if ($page->show_in_menu)
                        <td><input id="page_{!!$page->id!!}" type="checkbox" checked onchange="show_in_menu('{!! $page->id !!}')"></td>
                    @else
                        <td><input id="page_{!!$page->id!!}" type="checkbox" onchange="show_in_menu('{!! $page->id !!}')"></td>
                    @endif
                    <td><a href="/build/{!! $site->slug !!}/pages/{!!$page->id!!}" >{!! $page->title !!}</a></td>
                    <td>{!! $page->short_name !!}</td>
                    <td>{!! $page->description !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@stop

@section('scripts')
    <?php
    JavaScript::put([
            'ajaxPath' => Util::ajaxPath('build', 'pages'),
    ]);
    ?>
    <script>


        function show_in_menu(id) {
            var isChecked = $("#page_"+id).is(':checked');
            var source =GBEVars.ajaxPath + "/show_in_menu?page="+id+"&show="+isChecked;
            $.get( source, function( r ) {
            }).done(function(r) {
                $("#flash").text(r.message);
            }).fail(function(r) {
                $("#flash").text("Error saving menu information: "+r.responseJSON.error.message);
                $("#site_"+id).prop('checked', !isChecked);
            });
        }

        // Return a helper with preserved width of cells
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("#sort tbody").sortable({
            helper: fixHelper,
            update: function (event, ui) {
                var ordinal = 1;
                var changes = [];
                $("#sort tr.page-row").each(function () {
                    var pageId;
                    pageId = this.id.substr(4);
                    var newOrdinal = ordinal++;
                    var transform = {
                            "id": parseInt(pageId),
                            "ord": newOrdinal
                        };
                    changes.push(transform);
                });
                if (changes.length > 0) {
                    // Send to the server
                    var source = GBEVars.ajaxPath + "/setOrdinals?changes=" + JSON.stringify(changes);
                    $.get(source, function (r) {
                    }).done(function (r) {
                        $("#flash").text(r.message);
                    }).fail(function (r) {
                        $("#flash").text("Error saving order changes : " + r.responseJSON.error.message);
                    });
                }
            }
        }).disableSelection();



        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#sort2 tbody").sortable({
            helper: fixHelperModified

        }).disableSelection();


        $("#sort3 tbody").sortable().disableSelection();
    </script>
@stop