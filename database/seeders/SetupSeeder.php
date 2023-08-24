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
        $system_admin = Role::where('name', 'system-admin')->first();

        if (!$system_admin) {
            $system_admin = Role::create(['name' => 'system-admin', 'display_name' => '系统管理员', 'guard_name' => 'web', 'is_active' => 1]);
        }

        if (!$system_admin->data_scopes->count()) {
            $system_admin->initDataScope();
        }


        //创建系统管理部门
        if (!Department::find(1)) {
            Department::create([
                "id" => 1,
                "parent_id" => 0,
                "creator_id" => 1,
                "principal_id" => 0,
                "name" => "系统管理部门",
                "is_active" => true
            ]);
        }
    }
}
