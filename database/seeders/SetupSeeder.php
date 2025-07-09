<?php

namespace Database\Seeders;

use App\Models\Department;
use Modules\Permission\Entities\Role;
use Illuminate\Database\Seeder;


class SetupSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
		/********初始化角色********/

		//系统管理员
		$system_admin = Role::where('name', '系统管理员')->first();

		if (!$system_admin) {
			$system_admin = Role::create(['name' => '系统管理员', 'guard_name' => 'web', 'is_active' => 1, 'is_inherent' => 1]);
		}

		if (!$system_admin->dataScopes->count()) {
			$system_admin->initDataScope();
		}

		/**
		 * Demo 添加一些固定业务角色
		 */
		/*if(!Role::where('name', config('conf.role_x'))->exists()){
			Role::create(['name' => config('conf.role_x'), 'guard_name' => 'web', 'is_active' => 1, 'is_inherent' => 1, 'description' => '固定角色，不能删除']);
		}*/


		/********初始化部门********/

		//创建系统管理部门
		if (!Department::find(config('conf.system_department_id'))) {
			Department::create([
				"id" => config('conf.system_department_id'),
				"parent_id" => null,
				"creator_id" => config('conf.super_admin_id'),
				"principal_id" => 0,
				"name" => "系统管理部门",
				"is_active" => true
			]);
		}


		/********初始化审核流程********/

		/*$role_map = Role::get()->mapWithKeys(fn($item) => [$item->name => $item->id]);

		if (!ApprovalProcess::where('name', '院校二级审核')->exists()) {
			$process = ApprovalProcess::create([
				'name' => '院校二级审核',
				'creator_id' => config('conf.super_admin_id'),
				'subsequent_action' => 'invisible',
				'is_active' => 1,
				'remark' => '院系管理员 -> 校级管理员 \n系统基础审核流程，可以进行文案更改，非必要勿动审核节点！'
			]);

			$process->nodes()->create([
				'name' => '院系管理员',
				'creator_id' => config('conf.super_admin_id'),
				'approver_type' => ApproverTypes::DesignatedRole,
				'approver_id' => $role_map[config('conf.role_college_manager')],
				'weight' => 0,
			]);
			$process->nodes()->create([
				'name' => '校级管理员',
				'creator_id' => config('conf.super_admin_id'),
				'approver_type' => ApproverTypes::DesignatedRole,
				'approver_id' => $role_map[config('conf.role_school_manager')],
				'weight' => 0,
			]);
		}*/


		/********初始化业务配置********/

		/*$configurations = [
			['module' => 'affair', 'group' => 'supervision', 'name' => 'daily_max_appointment_count', 'display_name' => '督导员每天最多可预约课程数', 'value' => 4, 'lang' => app()->getLocale()]
		];
		foreach ($configurations as $configuration) {
			if (!Configuration::where('module', $configuration['module'])->where('group', $configuration['group'])->where('name', $configuration['name'])->where('lang', $configuration['lang'])->exists()) {
				Configuration::create($configuration);
			}
		}*/
	}
}
