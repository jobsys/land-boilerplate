<?php

namespace App\Http\Controllers\Manager;

use App\Enums\UserConf;
use App\Http\Controllers\BaseManagerController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Inertia\Inertia;
use Modules\Starter\Enums\State;

class IndexController extends BaseManagerController
{

	public function pageDashboard()
	{
		$roles = auth()->user()->roles()->lazy()->map(fn($item) => $item->name);
		$departments = auth()->user()->departments()->lazy()->map(fn($item) => $item->name);
		$daily_functions = auth()->user()->configurations()->where('key', UserConf::daily_functions)->value('value');

		return Inertia::render('PageDashboard', [
			'roles' => $roles,
			'departments' => $departments,
			'dailyFunctions' => $daily_functions,
		]);
	}

	public function pageTodo()
	{
		return Inertia::render('PageTodo');
	}

	public function pageCenterProfile()
	{
		$user = User::with(['departments:id,name'])->find(auth()->id())->toArray();
		return Inertia::render('PageCenterProfile', [
				'user' => collect($user)->only([
					'name', 'work_num', 'phone', 'email', 'nickname', 'avatar', 'work_phone'
				])
			]
		);
	}

	public function pageCenterPassword()
	{
		return Inertia::render('PageCenterPassword', [
			'tip' => request('tip'),
			'sm2PublicKey' => config('conf.sm2_public_key'),
		]);
	}

	public function centerProfileEdit(Request $request)
	{
		list($input, $error) = land_form_validate(
			$request->only(['phone', 'email', 'avatar', 'nickname', 'work_phone']),
			[
				'phone' => 'bail|required|string'
			],
			[
				'phone.required' => '手机号不能为空',
				'phone.string' => '手机号格式错误',
			]
		);

		if ($error) {
			return $this->message($error);
		}

		$input['id'] = auth()->id();

		$unique = land_is_model_unique($input, User::class, 'phone', false);
		if (!$unique) {
			return $this->message('手机号码已存在');
		}
		$unique = land_is_model_unique($input, User::class, 'email', false);
		if (!$unique) {
			return $this->message('电子邮箱已经存在');
		}

		$result = User::where('id', auth()->id())->update($input);

		if ($result) {
			auth()->login(User::find(auth()->id()));
		}

		return $this->json(null, $result ? State::SUCCESS : State::FAIL);
	}

	public function centerPasswordEdit(Request $request, UserService $service)
	{
		list($input, $error) = land_form_validate(
			$request->only(['old_password', 'password']),
			[
				'old_password' => 'bail|required|string',
				'password' => 'bail|required|string',
			],
			[
				'old_password.required' => '旧密码不能为空',
				'old_password.string' => '旧密码格式错误',
				'password.required' => '新密码不能为空',
				'password.string' => '新密码格式错误'
			]
		);

		if ($error) {
			return $this->message($error);
		}

		$user = auth()->user();

		$old_password = land_sm2_decrypt($input['old_password']);
		$new_password = land_sm2_decrypt($input['password']);

		//为了兼容原系统加密方式，这里要按需判断
		if ($user->last_password_modify_at) {
			if ($user->password !== land_sm3($old_password . $user->password_salt)) {
				return $this->message('原密码不正确');
			}
		} else {
			if ($user->password !== md5(md5($old_password) . $user->password_salt)) {
				return $this->message('原密码不正确');
			}
		}

		list($result, $error) = $service->modifyPassword(auth()->id(), $new_password);

		if ($error) {
			return $this->message($error);
		}

		return $this->json();
	}

	public function personalConfiguration()
	{
		[$input, $error] = land_form_validate(
			request()->only(['key', 'value']),
			[
				'key' => 'bail|required|string',
			],
			['key' => '个人配置KEY'],
		);

		if ($error) {
			return $this->message($error);
		}

		auth()->user()->configurations()->updateOrCreate(['key' => $input['key']], ['value' => $input['value']]);

		return $this->json();
	}

}
