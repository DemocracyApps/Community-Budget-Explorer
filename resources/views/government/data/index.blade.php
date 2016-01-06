@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('government.tabs', ['page'=>'data'])

    @if ($dataError) 
        <br>
        <div class="row">
            <p class="error text-danger bg-danger"><b>There was no response from the dataserver. Please contact the server administrator.</b></p>
        </div>
        <br>
    @endif
    <div class="row">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <b>Data Sources</b>
                <button style="float:right; position:relative; right:50px; bottom:5px;" class="btn btn-success btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}/data/create'">Add Data Source</button>
            </div>
            <div class="panel-body">
                <p>The data sources configured here generate the datasets listed in the next section. Note that on-demand and uploaded datasets
                    will not appear immediately - wait a few minutes and refresh the page to see them.
                </p>
            </div>

            <!-- Table -->
            <table class="table">
                <tr>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Parameters</th>
                    <th></th>
                </tr>
                @foreach($dataSources as $item)
                    <?php 
                        if ($item['sourceType'] == 'api') {
                            $apiUrl = $item['endpoint'];
                            $apiUrlName = (strlen($apiUrl)<20)?$apiUrl:substr($apiUrl,0,17) . "...";
                        }
                    ?>
                    <tr>
                        <td>{!! ucfirst($item['status']) !!}</td>
                        @if ($item['sourceType'] == 'file')
                            <td>
                                <a href="/governments/{!! $organization->id !!}/data/upload?datasource={!! $item['id'] !!}"
                                   class="btn btn-primary btn-xs">Upload</a>
                            </td>
                        @else
                            <td>
                                @if ($item['frequency'] == 'ondemand')
                                    <a href="/governments/{!! $organization->id !!}/data/{!! $item['id'] !!}/execute"
                                       class="btn btn-primary btn-xs">Execute</a>
                                @else 
                                    <a href="/governments/{!! $organization->id !!}/data/activate?datasource={!! $item['id'] !!}&activated={!! $item['status']=='active'?1:0 !!}"
                                       class="btn btn-primary btn-xs">{!! $item['status'] == 'active'?"Deactivate":"Activate"!!}</a>
                                @endif
                            </td>
                        @endif

                        <td>{!! $item['name'] !!}</td>
                        <td>{!! ($item['sourceType'] == 'file')?'File Upload':'API' !!}</td>
                        <td>{!! $item['description'] !!}</td>
                        <td> 
                            @if ($item['sourceType'] == 'file')
                                <p>
                                    <ul>
                                        <li><b>Data Format:</b> {!! strtoupper($item['dataFormat']) !!}</li>
                                    </ul>
                                </p>
                            @elseif ($item['sourceType'] == 'api')
                                <p>
                                    <ul>
                                        <li><b>Frequency:</b> {!! strtoupper($item['frequency']) !!}</li>
                                        <li>
                                            <b>Endpoint:</b>
                                            <a href="{!! $apiUrl !!}">{!! $apiUrlName !!}</a>
                                        </li>
                                        <li><b>API Format:</b> {!! strtoupper($item['apiFormat']) !!}</li>
                                        <li><b>Data Format:</b> {!! strtoupper($item['dataFormat']) !!}</li>
                                    </ul>
                                </p>
                            @endif
                        </td>
                        <td>
                            <a href="/governments/{!! $organization->id !!}/data/{!! $item['id'] !!}/edit"
                               class="btn btn-primary btn-xs">Edit</a>
                            <a href="/governments/{!! $organization->id !!}/data/{!! $item['id'] !!}/delete"
                               class="btn btn-danger disabled btn-xs">Delete</a>
                        </td>
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
                <p>{!! $dataError?$dataErrorMessage:"All ok" !!}</p>
            </div>

            <!-- Table -->
            <table class="table">
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Year</th>
                <th>Datasource ID</th>
                <th>Last Updated</th>
                @foreach($datasets as $item)
                    <tr>
                        <td>{!! $item['id'] !!}</td>
                        <td>{!! $item['name'] !!}</td>
                        <td>{!! $item['type']!!}</td>
                        <td>{!! $item['year'] !!}</td>
                        <td>{!! $item['datasource_id'] !!} </td>
                        <td>{!! $item['updated_at'] !!}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop
