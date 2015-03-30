@extends('templates.default')

@section('content')

    <div class="row">
        <h1>{!! $organization->name !!} </h1>
    </div>
    @include('government.tabs', ['page'=>'users'])


    <form method="POST" action="/governments/{!!$organization->id !!}/users/{!! $orgUser->id !!}" accept-charset="UTF-8">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <h1>Edit {!! $organization->name !!} User</h1>

        <br>
        <div class="row">
            <div class="col-md-2">
                <b>Email address:</b>
            </div>
            <div class="col-md10">
                {!! $user->email !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <b>Name:</b>
            </div>
            <div class="col-md10">
                {!! $user->name !!}
            </div>
        </div>

        <div class="form-group">
            <select name='access' class="form-control">
                @if ($orgUser->access == 0)
                    <option value="0" selected>No privileges</option>
                @else
                    <option value="0">No privileges</option>
                @endif
                @if ($orgUser->access == 9)
                    <option value="9" selected>Administrator</option>
                @else
                    <option value="9" >Administrator</option>
                @endif
            </select>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
            <a href="/governments/{!! $organization->id !!}/users" class="btn btn-danger">Cancel</a>
        </div>

    </form>

@stop