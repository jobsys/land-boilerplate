<?php
return [
    [
        "displayName" => "工作台",
        "page" => "page.manager.dashboard",
        "icon" => "HomeOutlined",
    ],
    [
        "displayName" => "组织管理",
        "icon" => "ApartmentOutlined",
        "key" => "group_management",
        "children" => [
            [
                "displayName" => "部门管理",
                "page" => "page.manager.department"
            ],
            [
                "displayName" => "角色管理",
                "page" => "page.manager.permission.role"
            ],
            [
                "displayName" => "账号管理",
                "page" => "page.manager.user"
            ],
        ]
    ],
    [
        "displayName" => "个人中心",
        "icon" => "iconfont-user-line",
        "key"=> "user_center",
        "children" =>[
            [
                "displayName" => "个人设置",
                "page" => "page.manager.center.profile"
            ],
            [
                "displayName" => "修改密码",
                "page" => "page.manager.center.password"
            ]
        ]
    ],
    [
        "displayName" => "系统设置",
        "icon" => "iconfont-settings--line1",
        "key" => "system_management",
        "children" => [
            [
                "displayName" => "字典管理",
                "page" => "page.manager.starter.dict"
            ]
        ]
    ]
];
