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
	<div class="left-section">
		<div class="slogan-wrapper">
			<h1 class="slogan">
				<img src="{{asset('images/'. config('conf.customer_identify'). '/logo-badge-white.png')}}">
				欢迎使用学生工作管理系统
			</h1>
		</div>
	</div>
	<div class="right-section">
		<div class="login-container">
			<div class="screen">
				<div class="screen__content">
					<div class="logo-wrapper">
						<img class="logo"
							 src="{{asset('images/' . config('conf.customer_identify') . '/logo-large.png')}}"
							 alt="{{config('conf.customer_name')}}">
					</div>
					<div class="login">
						<button class="button login__button"
								onclick="location.href = '{{route('page.login')}}'">
							<span class="button__text">教职工登录</span>
							<span class="button__icon">
								<svg t="1744179543884" class="icon" viewBox="0 0 1024 1024" version="1.1"
									 xmlns="http://www.w3.org/2000/svg" p-id="5338" width="200" height="200"><path
										d="M787.968 849.305a34.516 34.516 0 0 0 69.031 0z m-620.968 0a34.516 34.516 0 0 0 69.031 0z m345 0l-26.91 21.525a34.509 34.509 0 0 0 53.815 0z m68.98-86.264l26.906 21.529a34.524 34.524 0 0 0 6.108-31.671z m-137.96 0l-33.015-10.142a34.533 34.533 0 0 0 6.056 31.671l26.91-21.529z m172.47-450.568a103.494 103.494 0 0 1-103.493 103.5v68.98a172.474 172.474 0 0 0 172.475-172.48z m-103.493 103.5a103.5 103.5 0 0 1-103.5-103.5h-68.98A172.483 172.483 0 0 0 511.999 484.95z m-103.5-103.5a103.494 103.494 0 0 1 103.502-103.489v-68.98a172.476 172.476 0 0 0-172.478 172.469z m103.502-103.489a103.49 103.49 0 0 1 103.493 103.489h68.98A172.467 172.467 0 0 0 511.999 140z m345 640.325c0-190.539-154.462-345-345-345v68.975a275.963 275.963 0 0 1 275.969 276.021z m-345-345c-190.54 0-345 154.462-345 345h69.031a275.97 275.97 0 0 1 275.972-276.025z m26.906 366.526l69.03-86.26-53.866-43.053-69.031 86.209 53.922 43.1z m75.036-117.931l-68.926-224.226-65.924 20.285 68.976 224.221 65.979-20.285z m-134.85-224.226l-69.036 224.221 65.981 20.285 68.98-224.22zM416.113 784.57l68.98 86.26 53.922-43.1-69.031-86.209-53.819 43.053z m0 0"
										fill="#666666" p-id="5339"></path></svg>
							</span>
						</button>
						<button class="button login__button"
								onclick="location.href = '{{route('page.student.login')}}'">
							<span class="button__text">学生登录</span>
							<span class="button__icon">
								<svg t="1744179581895" class="icon" viewBox="0 0 1024 1024" version="1.1"
									 xmlns="http://www.w3.org/2000/svg" p-id="5372" width="200" height="200"><path
										d="M512 32a224 224 0 1 0-0.064 448A224 224 0 0 0 512 32z m0 64a160 160 0 1 1 0 320 160 160 0 0 1 0-320z"
										fill="#333333" p-id="5373"></path><path
										d="M512 416a352 352 0 0 1 351.744 337.856L864 768h-64a288 288 0 0 0-575.68-13.568L224 768h-64A352 352 0 0 1 512 416z"
										fill="#333333" p-id="5374"></path><path
										d="M105.472 740.928a32 32 0 0 0-4.928 63.808c142.656 11.008 278.976 55.04 400.256 128.192a32 32 0 0 0 33.024-54.848 972.928 972.928 0 0 0-428.352-137.152z"
										fill="#333333" p-id="5375"></path><path
										d="M932.672 739.84a32 32 0 0 1 4.928 63.808c-142.72 10.944-279.04 55.04-400.32 128.128a32 32 0 1 1-33.024-54.784 972.928 972.928 0 0 1 428.416-137.152z"
										fill="#333333" p-id="5376"></path></svg>
							</span>
						</button>
						@if(config('conf.use_cas'))
							<button class="button login__button" onclick="location.href = '{{route('cas.login')}}'">
								<span class="button__text">学校统一身份认证登录</span>
								<span class="button__icon">
								<svg t="1744179203323" class="icon" viewBox="0 0 1024 1024" version="1.1"
									 xmlns="http://www.w3.org/2000/svg" p-id="2990" width="200" height="200"><path
										d="M947.2 102.4c-6.4-6.4-19.2-6.4-32-6.4-121.6 51.2-268.8 25.6-358.4-70.4L537.6 6.4c-12.8-6.4-38.4-6.4-51.2 0l-19.2 25.6c-96 89.6-236.8 121.6-358.4 64-12.8-6.4-25.6 0-32 6.4-6.4 6.4-12.8 19.2-12.8 32l25.6 275.2c25.6 262.4 172.8 492.8 403.2 614.4H512c6.4 0 12.8 0 12.8-6.4 230.4-128 384-358.4 403.2-614.4l32-268.8c0-12.8-6.4-25.6-12.8-32z m-76.8 294.4c-19.2 230.4-153.6 441.6-358.4 556.8-204.8-115.2-332.8-326.4-358.4-556.8l-19.2-224c134.4 38.4 275.2 0 377.6-96 102.4 102.4 243.2 134.4 377.6 96l-19.2 224z"
										p-id="2991"></path><path
										d="M710.4 326.4L480 563.2 377.6 454.4c-12.8-6.4-38.4-6.4-51.2 0s-6.4 38.4 0 51.2l128 128c6.4 6.4 19.2 6.4 25.6 6.4s19.2 0 25.6-6.4l256-256c12.8-12.8 12.8-32 0-44.8s-38.4-12.8-51.2-6.4z"
										p-id="2992"></path></svg>
							</span>
							</button>
						@endif
					</div>
				</div>
				<div class="screen__background">
					<span class="screen__background__shape screen__background__shape4"></span>
					<span class="screen__background__shape screen__background__shape3"></span>
					<span class="screen__background__shape screen__background__shape2"></span>
					<span class="screen__background__shape screen__background__shape1"></span>
				</div>
			</div>
		</div>
	</div>

	<-- <div class="copyright">
		<p>职迅学生工作管理系统 ©版权所属</p>
		<p>
			技术支持： <a href="https://jobsys.cn" target="_blank">职迅科技 JOBSYS.cn</a>
		</p>
	</div> -->
</div>

</body>
</html>
