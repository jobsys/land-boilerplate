<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="{{ asset('images/' . config('conf.customer_identify') .'/favicon.png') }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{config('app.name')}}</title>
	<style>

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			height: 100vh;
			background-image: url("{{ asset('images/'. config('conf.customer_identify').'/landing-bg.png') }}");
			background-size: cover;
		}

		.page-container {
			display: flex;
			height: 100vh;
			backdrop-filter: blur(4px);
			background-color: rgb(255 255 255 / 20%);
		}

		.left-section {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: flex-end;
		}

		.slogan-wrapper {
			display: flex;
			align-items: center;
			padding: 80px;
			backdrop-filter: blur(4px);
			background-color: #981a44;
		}

		.slogan {
			color: #fff;
			display: flex;
			align-items: center;
			font-size: 40px;
			order: 2;
			overflow: hidden;
			user-select: none;
			letter-spacing: 8px
		}

		.slogan img {
			width: 100px;
			margin-right: 20px;
		}

		.right-section {
			background: linear-gradient(90deg, #ffffff, #e4c5cf);
			width: 800px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-container {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.screen {
			background: linear-gradient(90deg, #981a44, #681933);
			position: relative;
			height: 600px;
			width: 360px;
			box-shadow: 0 0 24px #981a44;
			border-radius: 20px;
		}

		.screen__content {
			z-index: 1;
			position: relative;
			height: 100%;
		}

		.screen__background {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 0;
			-webkit-clip-path: inset(0 0 0 0);
			clip-path: inset(0 0 0 0);
		}

		.screen__background__shape {
			transform: rotate(45deg);
			position: absolute;
		}

		.screen__background__shape1 {
			height: 520px;
			width: 520px;
			background: #FFF;
			top: -50px;
			right: 120px;
			border-radius: 0 72px 0 0;
		}

		.screen__background__shape2 {
			height: 220px;
			width: 220px;
			background: #d82b5f;
			top: -172px;
			right: 0;
			border-radius: 32px;
		}

		.screen__background__shape3 {
			height: 540px;
			width: 190px;
			background: linear-gradient(270deg, #892e4c, #f5a6bf);
			top: -24px;
			right: 0;
			border-radius: 32px;
		}

		.screen__background__shape4 {
			height: 380px;
			width: 150px;
			background: #a7345a;
			top: 420px;
			right: 48px;
			border-radius: 60px;
		}

		.logo-wrapper {
			padding: 100px 40px 20px;
		}

		.logo {
			max-width: 100%;
		}

		.login {
			width: 320px;
			padding: 30px;
		}

		.login__button {
			background: #fff;
			font-size: 14px;
			margin-bottom: 30px;
			padding: 16px 20px;
			border-radius: 26px;
			border: 1px solid #D4D3E8;
			text-transform: uppercase;
			font-weight: 700;
			display: flex;
			align-items: center;
			width: 100%;
			color: #981a44;
			box-shadow: 0 2px 2px #830931;
			cursor: pointer;
			transition: .2s;
		}

		.login__button:active,
		.login__button:focus,
		.login__button:hover {
			border-color: #830931;
			outline: none;
		}

		.button__icon {
			margin-left: auto;
		}

		.button__icon .icon {
			width: 20px;
			height: 20px;

		}

		.button__icon svg path {
			fill: #cb3d6b;
		}

		.copyright {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			font-size: 12px;
			color: #ddd;
			padding: 10px;
			text-align: right;
		}

		.copyright a {
			color: #ddd;
			text-decoration: none;
		}

	</style>
</head>
<body>
<div class="page-container">
	Welcome to the {{config('app.name')}}
</div>
</body>
</html>
