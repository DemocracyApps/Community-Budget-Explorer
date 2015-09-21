@extends('templates.default')

@section('content')
    @include('system.tabs', ['page'=>'users'])

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
