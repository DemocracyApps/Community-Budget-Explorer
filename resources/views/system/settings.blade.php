@extends('templates.default')

@section('content')
    @include('system.tabs', ['page'=>'settings'])

  <h1>System-Wide Settings</h1>

  <table class="table">
    <tr>
      <th>Setting</th><th>Name</th><th>Stuff</th>
    </tr>
  </table>

@stop
