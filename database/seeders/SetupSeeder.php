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


		//创建系统管理部门
		if (!Department::find(1)) {
			Department::create([
				"id" => 1,
				"parent_id" => null,
				"creator_id" => 1,
				"principal_id" => 0,
				"name" => "系统管理部门",
				"is_active" => true
			]);
		}
	}
}
