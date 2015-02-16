@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation" class="active"><a href="/system/organizations">Organizations</a></li>
</ul>

<div class="row">
    <div class="col-xs-6">
        <h1>All Accounts</h1>
    </div>
    <div class="col-xs-6">
        <button style="width:100px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                onclick="window.location.href='/system/accounts/create?chart={!! $chart->id !!}'">New Account</button>
        <button style="margin-right:10px; width:100px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                onclick="window.location.href='/system/accounts/upload?chart={!! $chart->id !!}'">Upload</button>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <p><b>Organization:</b> {!! $organization->name !!}</p>
    </div>
    <div class="col-md-4">
        <p><b>Chart of Accounts:</b> {!! $chart->name !!}</p>
    </div>
</div>

<table class="table">

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
              <td> {!! DemocracyApps\GB\Accounts\Account::typeName($account->type) !!} </td>
              <td> <form method="GET" action="/system/accounts/{!! $account->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                      <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                      <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm"><b>Edit</b></button>
                  </form>
              </td>
              <td> <form method="POST" action="/system/accounts/{!! $account->id !!}" accept-charset="UTF-8" style="display:inline-block">
                      <input name="_method" type="hidden" value="DELETE">
                      <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                      <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm"><b>Delete</b></button>
                  </form>
              </td>

          </tr>
      @endforeach
  </table>
@stop
