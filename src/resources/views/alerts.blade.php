{{Session::get('infoMessage')}}
@foreach (['info', 'success', 'warning', 'danger'] as $type)
    @if(Session::has($type . 'Message') || isset(${$type . 'Message'}))
        <div class="alert alert-{{ $type }}" role="alert">
            {{ Session::get($type . 'Message') ?? ${$type . 'Message'} }}
            @if (Session::has($type . 'List'))
                <ul>
                    @foreach(Session::get($type . 'List') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
@endforeach
@if (isset($errors) && count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
