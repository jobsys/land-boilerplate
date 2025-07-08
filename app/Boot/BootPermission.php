<?php

namespace App\Boot;

use App\Models\Department;
use App\Models\User;

class BootPermission
{

	public static function permissions(): array
	{
		return [
			'page.manager.dashboard' => '工作台',
			'page.manager.todo' => [
				'display_name' => '待办事项',
				'children' => [
					'page.manager.todo.approval' => '审核待办',
				],
			],
			'page.manager.permission.role' => [
				'display_name' => '角色管理',
				'children' => [
					'api.manager.permission.role.edit' => '编辑角色',
					'api.manager.permission.role.items' => '角色列表',
					'api.manager.permission.role.delete' => '删除角色',
					'api.manager.permission.role.permission.items' => '角色权限列表',
					'api.manager.permission.role.permission.edit' => '编辑角色权限',
					'api.manager.permission.role.data-scope.items' => '角色数据权限列表',
					'api.manager.permission.role.data-scope.edit' => '编辑角色数据权限',
				]
			],
			'page.manager.department' => [
				'display_name' => '部门管理',
				'children' => [
					'api.manager.department.edit' => '编辑部门',
					'api.manager.department.items' => '查看部门列表',
					'api.manager.department.item' => '查看部门详情',
					'api.manager.department.delete' => '删除部门',
				]
			],
			'page.manager.user' => [
				'display_name' => '账号管理',
				'children' => [
					'api.manager.user.edit' => '编辑账号',
					'api.manager.user.items' => '查看账号列表',
					'api.manager.user.item' => '查看账号详情',
					'api.manager.user.delete' => '删除账号',
					'api.manager.user.department' => '分配部门',
					'api.manager.user.import' => '账号导入',
				]
			],
			'page.manager.center.profile' => [
				'display_name' => '个人设置',
				'children' => [
					'api.manager.center.profile.edit' => '个人资料修改',
				]
			],

			'page.manager.center.password' => [
				'display_name' => '修改密码',
				'children' => [
					'api.manager.center.password.edit' => '修改密码',
				]
			],
		];
	}

	public static function dataScopes(): array
	{
		return [
			'department_key' => 'department_id',
			'creator_key' => 'creator_id',
			'resources' => [
				[
					'displayName' => '部门数据',
					'name' => 'department',
					'type' => 'model',
					'model' => Department::class,
				],
				[
					'displayName' => '用户数据',
					'name' => 'user',
					'type' => 'model',
					'model' => User::class,
				],
				/*[
					'displayName' => '教学体系数据',
					'name' => 'teaching-system',
					'type' => 'rule',
					'withCustom' => true,
					'customOptions' => [
						[
							'displayName' => '院系管辖范围',
							'type' => 'select',
							'field' => 'college_id',
							'options' => function () {
								return College::orderByDesc('sort_order')->get(['name', 'id'])->map(fn(College $college) => ['label' => $college->name, 'value' => $college->id])->toArray();
							}
						],
						[
							'displayName' => '专业管辖范围',
							'type' => 'tree',
							'field' => 'major_id',
							'options' => function () {
								$colleges = College::orderByDesc('sort_order')->get(['id', 'name']);
								$majors = Major::withCount(['students'])->get(['name', 'id', 'college_id', 'duration']);
								$options = [];
								foreach ($colleges as $college) {
									$options[] = [
										'label' => $college->name,
										'value' => 'college@' . $college->id,
										'children' => $majors->where('college_id', $college->id)->values()->map(fn(Major $major) => ['label' => "{$major->name}【{$major->duration}年制】({$major->students_count}人)", 'value' => $major->id])->toArray()
									];
								}
								return $options;
							}
						],
						[
							'displayName' => '校区管辖范围',
							'type' => 'select',
							'field' => 'campus_id',
							'options' => function () {
								return Campus::get(['name', 'id'])->map(fn(Campus $campus) => ['label' => $campus->name, 'value' => $campus->id])->toArray();
							}
						],
						[
							'displayName' => '班级管辖范围',
							'type' => 'remote',
							'field' => 'stu_class_id',
							'remoteOptions' => [
								'url' => route('api.manager.stu-class.items'),
								'keyword' => 'name',
							],
						],
						[
							'displayName' => '年级管辖范围',
							'type' => 'select',
							'field' => 'stu_class.year',
							'options' => function () {
								return StuClass::select('year')->distinct()->pluck('year')
									->filter(fn($year) => boolval($year))->sortDesc()->values()
									->map(fn($year) => ['label' => $year, 'value' => $year])->toArray();
							}
						],
						[
							'displayName' => '专业年级联合管辖范围',
							'type' => 'tree',
							'field' => 'major_year',
							'options' => function () {

								$stu_class_years = StuClass::select('year')->distinct()->pluck('year')
									->filter(fn($year) => boolval($year))->sortDesc();

								$majors = Major::get(['name', 'id']);

								$options = [];
								foreach ($stu_class_years as $year) {
									$options[] = [
										'label' => $year,
										'value' => $year,
										'children' => $majors->map(fn(Major $major) => ['label' => $year . $major->name, 'value' => $major->id . '@' . $year])->toArray()
									];
								}
								return $options;
							}
						],
					],
				],*/
			]
		];
	}
}
