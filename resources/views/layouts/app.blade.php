<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--{!! Session::token() !!}-->
    <!--{!! "<b>asdoaisjdoa</b>" !!}-->  <!-- tags interpreted -->
    <script>
        window.App = {!! json_encode([
            //'csrfToken' => csrf_token(),
            'user' => Auth::user(),
            'signedIn' => Auth::check()
        ]) !!}
    </script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mystyle.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/0.12.0/trix.css">
    
    <style>
        body { padding-bottom: 100px;  } /*no effect*/
        .level { display: flex; align-items: center; }  /*df, aic,   VERTICAL align*/
        .level-item { margin-right: 1em; }
        .flex { flex: 1; } /*flex it as much as we can*/
        .mr-1 { margin-right: 1em; }
        .ml-a { margin-left: auto; }
        [v-cloak] { display: none; }
        .ais-highlight > em { background: yellow; font-style: normal; }
        trix-editor { background: white; }
        /*.activ>a {background-color: red !important}*/
    </style>
        
    @yield('head')
</head>
<body style="padding-bottom: 100px;">
    <div id="app">
        @include('layouts.nav')
        
        @yield('content')
        
        <flash message="{{ session('flash') }}"></flash>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
