@extends('templates.default')

@section('content')

    <div class="row">
        <div class="col-xs-6">
            <h1>Account Category: {!! $category->name !!}</h1> <p><a href="/governments/{!!$governmentId!!}/accounts?chart={!! $category->chart !!}">Return</a></p>
        </div>
        <div class="col-xs-6">
            <button style="width:110px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm disabled"
                    onclick="window.location.href='/governments/{!!$governmentId!!}/accountcategoryvalues/create?category={!! $category->id !!}'">New Value</button>
            <button style="margin-right:10px; width:110px; float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm"
                    onclick="window.location.href='/governments/{!!$governmentId!!}/accountcategories/upload?category={!! $category->id !!}'">Upload Values</button>
        </div>
    </div>

    <div class="row">
        <h3>Category Values</h3>
        <table class="table">
            <tr>
                <th> ID </th>
                <th> Name </th>
                <th> Code </th>
                <th>  </th>
                <th>  </th>
            </tr>
            @foreach ($values as $value)
                <tr>
                    <td> {!! $value->id !!} </td>
                    <td> {!! $value->name !!} </a> </td>
                    <td> {!! $value->code !!} </td>
                    <td> <form method="GET" action="/governments/{!!$governmentId!!}/accountcategoryvalues/{!! $value->id !!}/edit" accept-charset="UTF-8" style="display:inline-block">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button style="display:inline-block;" type="submit" class="btn btn-warning btn-sm disabled"><b>Edit</b></button>
                        </form>
                    </td>
                    <td> <form method="POST" action="/governments/{!!$governmentId!!}/accountcategoryvalues/{!! $value->id !!}" accept-charset="UTF-8" style="display:inline-block">
                            <input name="_method" type="hidden" value="DELETE">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button style="display:inline-block;" type="submit" class="btn btn-danger btn-sm disabled"><b>Delete</b></button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </table>
    </div>
@stop
