@extends('templates.default')

@section('content')

<div class="row">
    <h1>{!! $organization->name !!} </h1>
</div>
@include('government.tabs', ['page'=>'organization'])
<div class="row">
    <div class="col-xs-12">
        <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-success btn-sm" onclick="window.location.href='/governments/{!! $organization->id !!}'">Back</button>
    </div>
</div>

<div class="row">
    <p>{!! $dataset->name !!} </p>
</div>

<div class="row">
    <div class="col-md-6">
        <h3>Top-Level Category Revenue and Expense</h3>
    </div>
    <table class="table">
        <tr>
            <th> Name </th>
            <th> Revenue </th>
            <th> Expense </th>
        </tr>
        @foreach ($data as $ditem)
            <tr>
                <td> {!!  $ditem->name !!} </td>
                <td> {!!  $ditem->revenue !!} </td>
                <td> {!!  $ditem->expense !!} </td>
            </tr>
        @endforeach
    </table>
</div>
@stop
