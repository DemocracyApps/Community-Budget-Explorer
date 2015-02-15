
@extends('templates.default')

@section('content')
    <div class="row">
        <h1>Welcome to The Government Budget Explorer!</h1>
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in tortor ullamcorper, sodales enim quis, vehicula dui. Nulla faucibus dolor sit amet enim rhoncus rutrum. Aenean iaculis volutpat tellus, eget vulputate erat dictum ut. Nunc facilisis nisl erat, sed ornare libero lobortis at. Vestibulum eu elementum sem, nec ornare augue. Curabitur sagittis tellus at ante congue ultrices. Sed vel sagittis metus. Sed convallis, sapien eu fermentum eleifend, tortor enim consequat orci, a sagittis diam magna eu ligula. Sed dapibus facilisis nulla at tincidunt. Nulla blandit feugiat purus, a pulvinar ante. Vestibulum mollis elit ut risus facilisis, mattis venenatis metus iaculis. Fusce sed cursus sem, nec ornare erat. </p>
        <br>
    </div>
    <div class="row">
        <h2>Projects</h2>
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Description</th>
            </tr>
            @foreach ($projects as $project)
                @if ($project->getProperty("access") == "Open")
                    <tr>
                        <td><a href="/{{$project->id}}">{{$project->name}}<a></td>
                        <td>{{$project->description}}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
@stop

@section('footer_right')

@stop
