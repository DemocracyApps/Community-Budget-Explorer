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
                Data Sources
                <button style="float:right; position:relative; right:50px; bottom:5px;" class="btn btn-success btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}/datasource/create'">Add Data Source</button>
            </div>
            <div class="panel-body">
                <p>...</p>
            </div>

            <!-- Table -->
            <table class="table">
                ...
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Datasets</div>
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
