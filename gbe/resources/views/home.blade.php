
@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <p>The purpose of this site is to help communities with conversations about their priorities
                and how those relate
                to the ways that local governments raise and spend money.
                This platform is open and free. Budget sites like those below
                can be created by local governments or by groups or individual citizens using public data.</p>
            <p>If you are interested in getting a site set up for your community, have a question,
                or would like to help, please contact
                us <a href="https://docs.google.com/forms/d/10c7muM4_DTY4rhUnV3D9M7l7o5m4Z7f0P237u9R_Hj4/viewform?usp=send_form" target="_blank">here</a>.
            </p>
            <br>

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
