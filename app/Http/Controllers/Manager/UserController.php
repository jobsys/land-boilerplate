<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use App\Importers\UserImporter;
use App\Models\Department;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\ImportExport\Services\ImportExportService;
use Modules\Permission\Entities\Role;
use Modules\Starter\Emnus\State;

class UserController extends BaseManagerController
{
	public function pageUser()
	{
		$departments = Department::all()->map(function ($item) {
			$item->{'value'} = $item->id;
			$item->{'title'} = $item->name;
			return $item;
		});
		$departments = land_classify($departments);
		$roles = Role::orderBy('sort_order', 'DESC')->where('is_active', true)->get();

		return Inertia::render('PageUser', compact('departments', 'roles'));
	}

	public function items(Request $request)
	{
		$super_admin_name = config('conf.super_admin_name', false);

		$query = User::authorise()->with(['departments:id,name', 'roles:id,display_name'])
			->filterable([
				'role_id' => function ($builder, $query) {
					$role_id = $query['value'];
					if ($role_id == -1) {
						return $builder->doesntHave('roles');
					} else {
						return $builder->whereHas('roles', function ($query) use ($role_id) {
							$query->where('id', $role_id);
						});
					}
				},
				'department_id' => function ($builder, $query) {
					$department_id = $query['value'];
					if ($department_id == -1) {
						return $builder->doesntHave('departments');
					} else {
						return $builder->whereHas('departments', function ($query) use ($department_id) {
							$query->where('id', $department_id);
						});
					}
				}
			])
			->when($super_admin_name, function ($query, $super_admin_name) {
				$query->where('name', '<>', $super_admin_name);
			})->orderByDesc('created_at');

		$pagination = $query->paginate();


		log_access('查看用户列表');

		return $this->json($pagination);
	}

	public function item(Request $request, $id)
	{

		$super_admin_name = config('conf.super_admin_name', false);

		$item = User::authorise()->with(['roles:id,display_name', 'departments:id,name'])->where('id', $id)->when($super_admin_name, function ($query, $super_admin_name) {
			$query->where('name', '<>', $super_admin_name);
		})->first();

		if (!$item) {
			return $this->json(null, State::NOT_FOUND);
		}

		log_access('查看用户详情');

		return $this->json($item);
	}

	public function edit(Request $request, UserService $userService)
	{
		list($input, $error) = land_form_validate(
			$request->only(['id', 'name', 'nickname', 'work_num', 'avatar', 'phone', 'position', 'roles', 'departments', 'email', 'password']),
			[
				'name' => 'bail|required|string',
				'phone' => 'bail|required|string',
			],
			['name' => '用户名', 'phone' => '手机号码', 'departments' => '所属部门', 'roles' => '用户角色'],
		);

		if ($error) {
			return $this->message($error);
		}

		$roles = $input['roles'] ?? [];
		unset($input['roles']);
		$departments = $input['departments'] ?? [];
		unset($input['departments']);


		list($result, $error) = $userService->editUser($input, $roles, $departments);

		if ($error) {
			return $this->message($error);
		}

		log_access(isset($input['id']) && $input['id'] ? '编辑用户' : '新建用户', $result->id);

		return $this->json($error, $result ? State::SUCCESS : State::FAIL);

	}

	public function delete(Request $request)
	{

		list($input, $error) = land_form_validate(
			$request->only(['id']),
			[
				'id' => 'bail|required|numeric'
			],
		);

		if ($error) {
			return $this->message($error);
		}

		$item = User::authorise()->where('id', $input['id'])->first();

		if (!$item) {
			return $this->json(null, State::NOT_FOUND);
		}

		$result = $item->delete();

		log_access('删除用户', $item->id);

		return $this->json(null, $result ? State::SUCCESS : State::FAIL);
	}

	public function department(Request $request)
	{
		list($input, $error) = land_form_validate(
			$request->only(['user_ids', 'department_ids', 'mode']),
			[
				'user_ids' => 'bail|required|array',
				'department_ids' => 'bail|required|array',
				'mode' => 'bail|required|string'
			],
			['user_ids' => '用户', 'department_ids' => '分配部门', 'mode' => '分配方式'],
		);

		if ($error) {
			return $this->message($error);
		}

		$user_ids = $input['user_ids'];

		$department_ids = $input['department_ids'];

		$users = User::authorise()->whereIn('id', $user_ids)->get(['id']);

		foreach ($users as $user) {

			if ($input['mode'] === 'append') {
				$user->departments()->attach($department_ids);
			} else if ($input['mode'] === 'overwrite') {
				$user->departments()->sync($department_ids);
			}
		}

		return $this->json();


	}

	public function import(Request $request, ImportExportService $service)
	{

		$departmentMap = [];
		$roleMap = [];

		$departments = Department::get(['id', 'name']);
		$roles = Role::get(['id', 'display_name']);

		$departments->each(function (Department $department) use (&$departmentMap) {
			$departmentMap[$department->name] = $department->id;
		});
		$roles->each(function (Role $role) use (&$roleMap) {
			$roleMap[$role->display_name] = $role->id;
		});


		$fields = [
			['field' => 'name', 'label' => '姓名', 'rule' => 'required'],
			['field' => 'work_num', 'label' => '工号', 'rule' => 'nullable|unique:users'],
			['field' => 'phone', 'label' => '手机号码', 'rule' => 'required|unique:users|regex:/^1[3456789]\d{9}$/'],
			['field' => 'email', 'label' => '电子邮箱', 'rule' => 'nullable|email|unique:users'],
			['field' => 'role', 'label' => '用户角色', 'rule' => 'nullable|in:' . implode(',', $roles->pluck('display_name')->toArray())],
			['field' => 'department', 'label' => '所属部门', 'rule' => 'nullable|in:' . implode(',', $departments->pluck('name')->toArray())],
			['field' => 'position', 'label' => '职位'],
			['field' => 'password', 'label' => '初始密码'],
		];

		list($result, $error) = $service->import('用户信息导入', UserImporter::class, $fields, ['roleMap' => $roleMap, 'departmentMap' => $departmentMap]);


		if ($error) {
			return $this->message($error);
		}

		log_access('用户信息导入');

		return $this->json($result);
	}

}
