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

			$unique = land_is_model_unique($attrs, User::class, 'email', false);

			if (!$unique) {
				return [false, '电子邮箱已经存在'];
			}

			$unique = land_is_model_unique($attrs, User::class, 'work_num', false);
			if (!$unique) {
				return [false, '工号已经存在'];
			}


			if (!$existing_user) {
				$attrs['password_salt'] = Str::random(6);
				$attrs['password'] = land_sm3( $attrs['password'] . $attrs['password_salt']);
			} else if (isset($attrs['password']) && $attrs['password']) { //如果传了密码则重新修改密码
				if (!$existing_user->password_salt) {
					$attrs['password_salt'] = Str::random(6);
				}
				$attrs['password'] = land_sm3($attrs['password'] . $existing_user->password_salt);
			}
			$attrs['last_login_error_at'] = null;

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

		$result = $user->save();

		return [$result, null];
	}

	/*public function importUser($item, $batch_id)
	{
		$transferLogic = new TransferLogic(app());

		try {

			if (!isset($item['work_num'])) {
				$transferLogic->log($batch_id, '无', '工号为空');
				throw new \Exception();
			}

			if (!isset($item['name'])) {
				$transferLogic->log($batch_id, $item['work_num'], '姓名为空');
				throw new \Exception();
			}

			if (!isset($item['phone'])) {
				$transferLogic->log($batch_id, $item['work_num'], '手机号码为空');
				throw new \Exception();
			}

			//性别
			if (isset($item['gender']) && $item['gender'] == '男') {
				$item['gender'] = 1;
			} else {
				$item['gender'] = 2;
			}


			$job = $item['job'] ?? '';
			unset($item['job']);

			$department = null;
			if (isset($item['department'])) {
				$department = Department::where('name', $item['department'])->first();

				if (!$department) {
					$transferLogic->log($batch_id, $item['work_num'], "部门 {$item['department']} 不存在，请先创建部门");
					throw new \Exception();
				}

				unset($item['department']);
			}

			$user = User::updateOrCreate(['work_num' => $item['work_num']], $item);

			if ($department) {
				$department->users()->attach($user->id, ['job' => $job, 'role' => Department::DepartmentPosition['MEMBER']]);
			}

		} catch (\Exception $e) {
			Log::error(__FUNCTION__ . '::' . $e->getMessage());
		}
	}*/

}
