@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation" class="active"><a href="/system/users">Users</a></li>
  <li role="presentation"><a href="/system/governments">Governments</a></li>
    <li role="presentation"><a href="/system/media">Media</a></li>
</ul>
  <h1>Manage Users</h1>

  <table class="table">
    <tr>
      <th>User ID</th><th>User Name</th><th>Superuser?</th><th>Project Creator?</th><th>Edit</th>
    </tr>
      @foreach ($users as $user)
        <tr>
            <td>{!! $user->id !!}</td><td>{!! $user->name !!}</td><td>{!! $user->superuser?'Yes':'No' !!}</td><td>{!! $user->projectcreator?'Yes':'No' !!}</td>
            <td><a class="label label-info" style="position:relative; top:5px;"
                href="/system/users/{!! $user->id !!}/edit">Edit</a></td>
        </tr>
      @endforeach
  </table>

@stop
