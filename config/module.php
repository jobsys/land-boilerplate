<?php
return [
    "Permission" => [
        "permissions" => [
            "page.manager.dashboard" => "工作台",
            "page.manager.permission.role" => [
                "display_name" => "角色管理",
                "children" => [
                    "api.manager.permission.role.edit" => "编辑角色",
                    "api.manager.permission.role.items" => "角色列表",
                    "api.manager.permission.role.delete" => "删除角色",
                    "api.manager.permission.role.permission.items" => "角色权限列表",
                    "api.manager.permission.role.permission.edit" => "编辑角色权限",
                    "api.manager.permission.role.data-scope.items" => "角色数据权限列表",
                    "api.manager.permission.role.data-scope.edit" => "编辑角色数据权限",
                ]
            ],
            "page.manager.department" => [
                "display_name" => "部门管理",
                "children" => [
                    "api.manager.department.edit" => "编辑部门",
                    "api.manager.department.items" => "查看部门列表",
                    "api.manager.department.item" => "查看部门详情",
                    "api.manager.department.delete" => "删除部门",
                ]
            ],
            "page.manager.user" => [
                "display_name" => "账号管理",
                "children" => [
                    "api.manager.user.edit" => "编辑账号",
                    "api.manager.user.items" => "查看账号列表",
                    "api.manager.user.item" => "查看账号详情",
                    "api.manager.user.delete" => "删除账号",
                    "api.manager.user.department" => "分配部门",
                ]
            ],
            "page.manager.center.profile" => [
                "display_name" => "个人设置",
                "children" => [
                    "api.manager.center.profile.edit" => "个人资料修改",
                ]
            ],

            "page.manager.center.password" => [
                "display_name" => "修改密码",
                "children" => [
                    "api.manager.center.password.edit" => "修改密码",
                ]
            ],
        ],
        "data_scope" => [
            "department_key" => "department_id",
            "creator_key" => "creator_id",
            "resources" => [
                ['displayName' => '部门数据', 'name' => 'department', 'model' => \App\Models\Department::class],
                ['displayName' => '用户数据', 'name' => 'user', 'model' => \App\Models\User::class],
            ]
        ]
    ]
];