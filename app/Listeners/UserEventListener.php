<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login;

class UserEventListener
{
	public function handleUserLogin(Login $event): void
	{
		//后台非超管用户登录后触发
		if ($event->guard === 'web' && !$event->user->isSuperAdmin()) {
			$scope = $event->user->getDataScope();
			//TODO 可以根据用户的数据权限范围进行自定义操作
		}
	}
}
