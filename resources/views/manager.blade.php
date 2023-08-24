<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    {{Vite::useBuildDirectory('build/manager')->withEntryPoints(['resources/views/web/manager/main.js'])}}
    @inertiaHead
    <script>
        @if(Auth::check())
            window.starterInit = @json(starter_setup_user());
        @endif
    </script>
    <script>
    </script>
</head>
<body>
@inertia
</body>
</html>
