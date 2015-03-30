@extends('views.templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation" class="active"><a href="/system/organizations">Organizations</a></li>
</ul>

<div class="row">
    <div class="col-xs-6">
        <h1>Organizations</h1>
    </div>
    <div class="col-xs-6">
        <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/system/governments/create'">New</button>
    </div>
</div>

  <table class="table">
      <tr>
          <th> ID </th>
          <th> Name </th>
          <th>  </th>
          <th>  </th>
      </tr>
      @foreach ($organizations as $organization)
          <tr>
              <td> {!!  $organization->id !!} </td>
              <td> <a href="/system/organizations/{!! $organization->id !!}">{!!  $organization->name  !!} </a> </td>
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
@stop
