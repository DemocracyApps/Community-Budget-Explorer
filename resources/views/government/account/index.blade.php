@extends('templates.default')

@section('content')

    <div class="row">
        <div class="col-xs-6">
            <h1>Chart of Accounts</h1>
        </div>
        <div class="col-xs-6">
            <button style="width:110px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                    onclick="window.location.href='/governments/{!! $organization->id !!}/accounts/create?chart={!! $chart->id !!}'">New Account</button>
            <button style="margin-right:10px; width:110px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                    onclick="window.location.href='/governments/{!! $organization->id !!}/accounts/upload?chart={!! $chart->id !!}'">Upload Accounts</button>
            <button style="margin-right:10px; width:110px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                    onclick="window.location.href='/governments/{!! $organization->id !!}/accountcategories/create?chart={!! $chart->id !!}'">New Category</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <p><b>Organization:</b> <a href="/governments/{!!$organization->id!!}">{!! $organization->name !!}</a></p>
        </div>
        <div class="col-md-4">
            <p><b>Chart of Accounts:</b> {!! $chart->name !!}</p>
        </div>
    </div>
    <!-- Account Categories -->
    <div class="row">
        <h3>Categories</h3>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Name</th>
            </tr>
            @foreach ($categories as $category)
                <tr>
                    <td>{!! $category->id !!}</td>
                    <td><a href="/governments/{!! $organization->id !!}/accountcategories/{!! $category->id !!}"> {!! $category->name !!} </a></td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="row">
        <div class="col-md-offset-2"> </div>
        <div class="col-md-10">
            <h4>Default Category Sequence</h4>

        </div>
    </div>

    <!-- Account Categories -->

    <div class="row">
        <h3>Accounts</h3>
        <table class="table">
            <tr>
                <th> Account ID </th>
                <th> Account Name </th>
                <th> Account Code </th>
                <th> Type </th>
                <th>  </th>
                <th>  </th>
            </tr>
            @foreach ($accounts as $account)
                <tr>
                    <td> {!! $account->id !!} </td>
                    <td> {!! $account->name !!} </a> </td>
                    <td> {!! $account->code !!} </td>
                    <td> {!! DemocracyApps\GB\Budget\Account::typeName($account->type) !!} </td>
                    <td> <form method="GET" action="/governments/{!! $organization->id !!}/accounts/{!! $account->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                        </form>
                    </td>
                    <td> <form method="POST" action="/governments/{!! $organization->id !!}/accounts/{!! $account->id !!}" accept-charset="UTF-8" style="display:inline-block">
                            <input name="_method" type="hidden" value="DELETE">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </table>
    </div>
@stop
