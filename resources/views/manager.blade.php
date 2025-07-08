<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="{{ asset('images/' . config('conf.customer_identify') .'/favicon.png') }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{config('app.name')}}</title>
	<link rel="stylesheet" type="text/css" media="print" href="{{asset('print-lock.css')}}">
	{{Vite::useBuildDirectory('build/manager')->withEntryPoints(['resources/views/web/manager/main.js'])}}
	@inertiaHead
	<script>
		window.landAppInit = @json(starter_setup());

		@if(Auth::check())
			window.isLogin = true;
		window.landUserSetup = @json(starter_setup_user());
		@else
			window.isLogin = false;
		@endif
	</script>
</head>
<body>
@unless(is_browser_compatibility())
	<div id="browser-warning"
		 style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; text-align: center;">
		<h2 style="margin-bottom: 20px;">您的浏览器版本过低或不受支持</h2>
		<p style="margin-bottom: 30px;">为了获得最佳体验，请下载并使用以下推荐的浏览器：</p>

		<div style="display: flex; gap: 20px; margin-bottom: 30px;">
			<a href="https://www.microsoft.com/zh-cn/edge/download" target="_blank" style="text-decoration: none;">
				<div style="display: flex; flex-direction: column; align-items: center;">
					<img src="{{ asset('images/browsers/edge.png') }}" width="60" height="60" alt="Edge">
					<span style="margin-top: 10px;">Microsoft Edge</span>
				</div>
			</a>
			<a href="https://www.google.cn/chrome/?standalone=1" target="_blank" style="text-decoration: none;">
				<div style="display: flex; flex-direction: column; align-items: center;">
					<img src="{{ asset('images/browsers/chrome.png') }}" width="60" height="60" alt="Chrome">
					<span style="margin-top: 10px;">Google Chrome</span>
				</div>
			</a>
			<a href="https://www.firefox.com.cn/" target="_blank" style="text-decoration: none;">
				<div style="display: flex; flex-direction: column; align-items: center;">
					<img src="{{ asset('images/browsers/firefox.png') }}" width="60" height="60" alt="Firefox">
					<span style="margin-top: 10px;">Mozilla Firefox</span>
				</div>
			</a>
			<a href="https://www.apple.com/safari/" target="_blank" style="text-decoration: none;">
				<div style="display: flex; flex-direction: column; align-items: center;">
					<img src="{{ asset('images/browsers/safari.png') }}" width="60" height="60" alt="Safari">
					<span style="margin-top: 10px;">Apple Safari (MacOS 平台)</span>
				</div>
			</a>
		</div>

		<button onclick="document.getElementById('browser-warning').style.display='none'"
				style="padding: 10px 20px; background: #f0f0f0; border: none; border-radius: 4px; cursor: pointer;">
			我了解风险，继续使用
		</button>
	</div>
@endunless
@inertia
</body>
</html>
