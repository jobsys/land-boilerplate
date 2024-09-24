<?php

namespace App\Providers;

use Browser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Request;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{

		//关闭 Model 字段写入保护
		Model::unguard();

		//将Carbon的语言设置为中文
		Carbon::setLocale('zh');

		//PHP 时区
		date_default_timezone_set(config('app.timezone'));

		//PHP 超时
		set_time_limit(300);

		//设置数据库默认字符串长度，超过该长度 MySQL 无法创建索引
		Schema::defaultStringLength(191);

		//根据配置决定是否开户 HTTPS
		if (config('conf.use_https')) {
			URL::forceScheme('https');
		}

		//为活动日志添加额外信息
		Activity::saving(function (Activity $activity) {
			$activity->properties = $activity->properties->put('agent', [
				'ip' => Request::header('x-forwarded-for') ?? Request::ip(),
				'browser' => Browser::browserName() . ' ' . Browser::browserVersion(),
				'os' => Browser::platformName() . ' ' . Browser::platformVersion(),
				'url' => Request::fullUrl(),
			]);
		});

		//为本地存储设置临时链接
		Storage::disk('private')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
			return URL::temporarySignedRoute(
				'temp.file',
				$expiration,
				array_merge($options, ['path' => $path])
			);
		});
	}
}
