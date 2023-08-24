<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        //将Carbon的语言设置为中文
        Carbon::setLocale('zh');

        //设置数据库默认字符串长度，超过该长度 MySQL 无法创建索引
        Schema::defaultStringLength(191);

        //根据配置决定是否开户 HTTPS
        if (config('conf.use_https')) {
            URL::forceScheme('https');
        }

        //为本地存储设置临时链接
        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            //由于本地存储的路径中包含了 /，所以需要将 / 替换为 -，避免生成的临时链接中出现 / 导致路由混淆
            return URL::temporarySignedRoute(
                'local.temp',
                $expiration,
                array_merge($options, ['path' => str_replace('/', '-', $path)])
            );
        });
    }
}
