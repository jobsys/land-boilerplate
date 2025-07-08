<?php

namespace App\Http\Controllers;

use App\Models\User;
use Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Permission\Services\PermissionService;
use Modules\Starter\Http\Controllers\BaseController;
use Modules\Wechat\Enums\WechatSns;
use Modules\Wechat\Services\WechatService;

class BaseManagerController extends BaseController
{
	public function __construct()
	{
		$this->initializeMiddlewares();
	}

	protected function initializeMiddlewares()
	{
		if (Browser::isMobile()) {
			$this->handleMobileRequest();
		} else {
			$this->handleWebRequest();
		}
	}

	protected function handleMobileRequest()
	{
		if (config('conf.use_wechat_channel') && config('app.env') !== 'local') {
			$this->handleWechatMobileAuth();
		} else {
			$this->handleLocalMobileAuth();
		}

		$this->middleware(function ($request, $next) {
			Inertia::setRootView('mobile-manager');
			return $next($request);
		});
	}

	protected function handleWechatMobileAuth()
	{
		$this->middleware(function (Request $request, $next) {
			if ($request->route()->getName() === 'page.mobile.manager.error') {
				return $next($request);
			}

			if (!session('sns_user')) {
				return $this->redirectWechatAuth($request);
			}

			return $next($request);
		});
	}

	protected function redirectWechatAuth(Request $request)
	{
		session(['intend_url' => $request->fullUrl()]);
		$wechatService = app(WechatService::class);
		$wechat_channel = config('conf.use_wechat_channel');


		//只要有配置企业微信就优先使用企业微信
		if ($wechatService->requestFrom(WechatSns::Work) || Str::contains($wechat_channel, WechatSns::Work)) {
			return response()->redirectTo(route('wechat.work.redirect', ['role' => 'manager']));
		}

		if ($wechatService->requestFrom(WechatSns::OfficialAccount) && Str::contains($wechat_channel, WechatSns::OfficialAccount)) {
			return response()->redirectTo(route('wechat.official.redirect', ['role' => 'manager']));
		}

		return response()->redirectTo(route('page.mobile.manager.error', ['message' => '未配置微信渠道']));
	}

	protected function handleLocalMobileAuth()
	{
		$dev_user_id = config('dev.dev_user_id');
		if ($dev_user_id && (!auth()->check() || auth()->id() !== $dev_user_id)) {
			auth()->login(User::find($dev_user_id));
		}
	}

	protected function handleWebRequest()
	{
		$this->middleware(function ($request, $next) {
			if (!auth()->check()) {
				return $request->expectsJson()
					? response('', 401)
					: response()->redirectTo(route('page.login'));
			}

			if (!$this->checkPermission()) {
				return response('', 403);
			}

			$this->handleSessionLifetime();

			if ($this->shouldForcePasswordChange($request)) {
				return response()->redirectTo(route('page.manager.center.password', [
					'tip' => '您的密码已经过期，请先修改密码'
				]));
			}

			return $next($request);
		});

		$this->middleware(function ($request, $next) {
			Inertia::setRootView('manager');
			return $next($request);
		});
	}


	protected function checkPermission(): bool
	{
		$service = app(PermissionService::class);
		return $service->can(Route::current()->getName());
	}

	protected function handleSessionLifetime()
	{
		$session_lifetime = configuration_get_first('system', 'security', 'security_auto_logout_time', 120);
		config(['session.lifetime' => $session_lifetime]);
	}

	protected function shouldForcePasswordChange(Request $request): bool
	{
		return cache('security_should_user_modify_password') &&
			!in_array($request->route()->getName(), [
				'page.manager.center.password',
				'api.manager.center.password.edit'
			]);
	}
}
