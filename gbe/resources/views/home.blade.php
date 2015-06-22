
@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <p>This site is still under development, but feel free to explore community budgets listed below. </p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Site Name</th><th>Community Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sites as $site)
                        <tr>
                            <td><a href="/sites/{!! $site->slug !!}">{!! $site->name !!}</a> </td>
                            <td>{!! $site->governmentName !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('footer_right')

@stop
