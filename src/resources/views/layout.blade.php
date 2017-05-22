<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{asset('loyaltycorp-test/css/app.css')}}">
    </head>
    <body>
        @hasSection('sidebar')
            <div class="container">
                <div class="row sidebar-layout">
                    <div class="col-md-2 sidebar">
                        @yield('sidebar')
                    </div>
                    <div class="col-md-10 content">
                        @yield('content')
                    </div>
                </div>
            </div>
        @else
            @yield('content')
        @endif
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="{{asset('loyaltycorp-test/js/app.js')}}" async></script>
    </body>
</html>
