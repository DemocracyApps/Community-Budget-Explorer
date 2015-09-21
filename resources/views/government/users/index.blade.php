@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('government.tabs', ['page'=>'users'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-10px;" class="btn btn-success btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}/users/create'">Add User</button>
        </div>
    </div>
    <br>
    <div class="row">
        <table class="table">
            <tr>
                <th>User ID</th>
                <th>User Name</th>
                <th>Access Level</th>
                <th></th>
                <th></th>
            </tr>
            @foreach ($users as $mUser)
                <tr>
                    <td>{!! $mUser->user_id !!}</td>
                    <td>{!! $userMap[$mUser->user_id]->name !!}</td>
                    <td>{!! $accessMap[$mUser->access] !!}</td>
                    <td> <form method="GET" action="/governments/{!! $organization->id !!}/users/{!! $mUser->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button style="display:inline-block;" type="submit" class=" btn btn-warning btn-sm"><b>Edit</b></button>
                        </form>
                    </td>
                    <td> <form method="POST" action="/governments/{!! $organization->id !!}/users/{!! $mUser->id !!}" accept-charset="UTF-8" style="display:inline-block">
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
