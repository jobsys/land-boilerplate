<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="{{ asset('images/' . config('conf.customer_identify') .'/favicon.png') }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{config('app.name')}}</title>
</head>
<body>
<div id="app">
	欢迎使用 {{config('app.name')}}
</div>
</body>
</html>
