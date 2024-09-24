<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login;

class UserEventListener
{
	public function handleUserLogin(Login $event): void
	{
		//后台非超管用户登录后触发
		if ($event->guard === 'web' && !$event->user->isSuperAdmin()) {
			/*$auth_student_ids = Student::authorise()->get(['id'])->pluck('id')->toArray();
			session(['auth_student_ids' => $auth_student_ids]);*/
		}
	}
}
