@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation"><a href="/system/governments">Governments</a></li>
    <li role="presentation"><a href="/system/media">Media</a></li>
</ul>
  <h1>System-Wide Settings</h1>

  <table class="table">
    <tr>
      <th>Setting</th><th>Name</th><th>Stuff</th>
    </tr>
  </table>

@stop
