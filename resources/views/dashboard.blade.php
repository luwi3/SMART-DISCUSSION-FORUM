@if(Auth::user()->role === 'Administrator')
    @include('dashboards.admin')

@elseif(Auth::user()->role === 'Lecturer')
    @include('dashboards.lecturer')

@else
    @include('dashboards.student')
@endif