@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('government.tabs', ['page'=>'data'])

    <div class="row">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <b>Data Sources</b>
                <button style="float:right; position:relative; right:50px; bottom:5px;" class="btn btn-success btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}/data/create'">Add Data Source</button>
            </div>
            <div class="panel-body">
                <p>The data sources configured here give rise to the datasets listed in the next section below. Datasets
                    will not appear immediately - wait a few minutes and refresh the page to see them.
                </p>
            </div>

            <!-- Table -->
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Source Type</th>
                    <th>Description</th>
                    <th>Action</th>
                    <th>Last Updated</th>
                </tr>
                @foreach($dataSources as $item)
                    <tr>
                        <td>{!! $item->id !!}</td>
                        <td>{!! $item->name !!}</td>
                        <td>{!! ($item->source_type == 'file')?'File Upload':'API' !!}</td>
                        <td>{!! $item->description !!}</td>
                        @if ($item->source_type == 'file')
                            <td>
                                <a href="/governments/{!! $organization->id !!}/data/upload?datasource={!! $item->id !!}"
                                   class="btn btn-primary btn-xs">Upload</a>
                            </td>
                        @else
                            <td>TBD</td>
                        @endif
                        <td> {!! ($item->last_update == null)?'Never':date('M d, Y', strtotime($item->last_update)) !!}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading"><b>Datasets</b></div>
            <div class="panel-body">
                <p>...</p>
            </div>

            <!-- Table -->
            <table class="table">
                ...
            </table>
        </div>
    </div>

@stop
