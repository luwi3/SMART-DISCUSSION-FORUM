
<h2>Notifications</h2>

@foreach($notifications as $notification)

<div>

    <p>
        {{ $notification->data['message'] }}
    </p>

    <a href="{{ $notification->data['url'] }}">
        Open
    </a>

</div>

<hr>

@endforeach