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
        <div class="col-xs-12">
            <h2>Rows</h2>
            <a style="float:right; position:relative; right:10px; bottom:35px;" class="btn btn-primary btn-sm"
                    href='/build/{!! $site->slug !!}/pages/{!! $page->id !!}/rows/create'>New Row</a>
        </div>
        <div class="col-xs-12">
            <table class="table">
                <thead>
                    <th>Row ID</th><th>Row Title</th><th>Layout</th>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>{!! $row->id !!}</td>
                            <td>{!! $row->title !!}</td>
                            <td>Layout</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop
