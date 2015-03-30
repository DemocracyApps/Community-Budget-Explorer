@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('media.tabs', ['page'=>'sites'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-10px;" class="btn btn-success btn-sm" onclick="window.location.href='/media/{!! $organization->id !!}/sites/create'">Add Site</button>
        </div>
    </div>
    <br>
    <div class="row">
        <table class="table">
            <tr>
                <th>Site Slug</th>
                <th>Site Name</th>
                <th>Published?</th>
            </tr>
            @foreach ($sites as $site)
                <tr>
                    <td>{!! $site->slug !!}</td>
                    <td>{!! $site->name !!}</td>
                    @if ($site->published)
                        <td><input id="site_{!!$site->id!!}" type="checkbox" checked onchange="publish('{!! $site->id !!}')"></td>
                    @else
                        <td><input id="site_{!!$site->id!!}" type="checkbox" onchange="publish('{!! $site->id !!}')"></td>
                    @endif

                </tr>
            @endforeach
        </table>
    </div>

@stop

@section('scripts')
    <?php
    JavaScript::put([
            'ajaxPath' => Util::ajaxPath('mediaAdmin', 'sites'),
    ]);
    ?>
    <script>
        $(function() {
            //$( "#datepicker" ).datepicker();
        });

        function publish(id) {
            var isChecked = $("#site_"+id).is(':checked');
            var source =GBEVars.ajaxPath + "/publish?site="+id+"&published="+isChecked;
            $.get( source, function( r ) {
            }).done(function(r) {
                $("#flash").text(r.message);
            }).fail(function(r) {
                $("#flash").text("Error saving publish information: "+r.responseJSON.error.message);
                $("#site_"+id).prop('checked', !isChecked);
            });
        }
    </script>
@stop