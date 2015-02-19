@extends('templates.default')

@section ('buttons')
    <a href="/system/organizations" class="btn btn-info">Back to All Organizations</a>
@stop

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation" class="active"><a href="/system/organizations">Organizations</a></li>
</ul>

<div class="row">
    <div class="col-xs-6">
        <h1>{!! $organization->name !!} </h1>
    </div>
    <div class="col-xs-6">
        <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/system/organizations/{!! $organization->id !!}/edit'">Edit</button>
    </div>
</div>

<div class="row">
    <h3>Charts of Accounts</h3>
    <table class="table">
        <tr>
            <th> ID </th>
            <th> Name </th>
            <th>  </th>
            <th>  </th>
        </tr>
        @foreach ($charts as $chart)
            <tr>
                <td> {!!  $chart->id !!} </td>
                <td> <a href="/system/accounts?chart={!!$chart->id!!}"> {!!  $chart->name  !!} </a> </td>
                <td> <form method="GET" action="/system/organizations/{!! $organization->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                    </form>
                </td>
                <td> <form method="POST" action="/system/organizations/{!! $organization->id !!}" accept-charset="UTF-8" style="display:inline-block">
                        <input name="_method" type="hidden" value="DELETE">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<br>
<div class="row">
    <div class="col-md-6">
        <h3>Data Sets</h3>
    </div>
    <div class="col-md-6">
        <a href="/system/datasets/create?organization={!! $organization->id!!}&chart={!! $charts[0]->id !!}"
           class="btn btn-primary btn-sm" style="float:right;">New Dataset</a>
    </div>
    <table class="table">
        <tr>
            <th> ID </th>
            <th> Name </th>
            <th> Year </th>
            <th> Type </th>
            <th>  </th>
            <th>  </th>
        </tr>
        @foreach ($datasets as $dataset)
            <tr>
                <td> {!!  $dataset->id !!} </td>
                <td> <a href="/system/datasets/{!!$dataset->id!!}"> {!!  $dataset->name  !!} </a> </td>
                <td> {!! $dataset->year !!} </td>
                <td> {!! ucfirst($dataset->type) !!} </td>
                <td> <form method="GET" action="/system/datasets/{!! $dataset->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="disabled btn btn-warning btn-sm"><b>Edit</b></button>
                    </form>
                </td>
                <td> <form method="POST" action="/system/datasets/{!! $dataset->id !!}" accept-charset="UTF-8" style="display:inline-block">
                        <input name="_method" type="hidden" value="DELETE">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <button style="display:inline-block;" type="submit" class="disabled btn btn-danger btn-sm"><b>Delete</b></button>
                    </form>
                </td>

            </tr>
        @endforeach
    </table>
</div>
@stop
