<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Starter\Services\BaseService;

class UserService extends BaseService
{

	/**
	 * 用户编辑/创建
	 * @param $attrs
	 * @param $roles
	 * @param $departments
	 * @return array
	 */
	public function editUser($attrs, $roles, $departments): array
	{
		try {

			$existing_user = User::find($attrs['id'] ?? null);

			$unique = land_is_model_unique($attrs, User::class, 'phone', false);

			if (!$unique) {
				return [false, '手机号码已存在'];
			}

			/*$unique = land_is_model_unique($attrs, User::class, 'email', false);

			if (!$unique) {
				return [false, '电子邮箱已经存在'];
			}*/

			$unique = land_is_model_unique($attrs, User::class, 'work_num', false);
			if (!$unique) {
				return [false, '工号已经存在'];
			}

			$password = isset($attrs['password']) && $attrs['password'] ? land_sm2_decrypt($attrs['password']) : null;

			if (!$password) {
				unset($attrs['password']);
			}

			if (!$existing_user) {
				$attrs['password_salt'] = Str::random(6);
				$attrs['password'] = land_sm3($password . $attrs['password_salt']);
			} else if (isset($attrs['password']) && $attrs['password']) { //如果传了密码则重新修改密码
				if (!$existing_user->password_salt) {
					$attrs['password_salt'] = Str::random(6);
				}
				$attrs['password'] = land_sm3($password . $existing_user->password_salt);
			}
			$attrs['last_login_error_at'] = null;
			$attrs['login_error_count'] = 0;

			$user = User::updateOrCreate(['id' => $attrs['id'] ?? null], $attrs);

			if (!empty($roles)) {
				$user->roles()->sync($roles);
			}

			if (!empty($departments)) {
				$user->departments()->sync($departments);
			}
		} catch (\Exception $e) {
			Log::error("[Edit User]:" . $e->getMessage());
			return [false, '保存用户信息失败'];
		}
		return [$user, null];
	}


	/**
	 * 修改密码
	 * @param $user_id
	 * @param $password
	 * @return array
	 */
	public function modifyPassword($user_id, $password): array
	{
		$user = User::find($user_id);
		if (!$user) {
			return [false, '用户不存在'];
		}

		if (!$user->password_salt) {
			$user->password_salt = Str::random(6);
		}


		$user->password = land_sm3($password . $user->password_salt);
		$user->last_password_modify_at = now();

		$result = $user->save();

		log_access('修改密码', $user);


		cache()->forget('security_should_user_modify_password');

		return [$result, null];
	}

}
