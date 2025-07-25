<?php
return [
	[
		"displayName" => "工作台",
		"page" => "page.manager.dashboard",
		"icon" => "HomeOutlined",
	],
	[
		"displayName" => "我的事务",
		"key" => "my_task_manager",
		"icon" => "UserOutlined",
		"children" => [
			[
				"displayName" => "待办事项",
				"page" => 'page.manager.todo'
			],
		]
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
		'displayName' => '系统工具',
		'icon' => 'ToolOutlined',
		'key' => 'tool_manager',
		'children' => [
			[
				'displayName' => '数据传输',
				'page' => 'page.manager.tool.data-transfer',
			],
			[
				"displayName" => "消息管理",
				"page" => "page.manager.starter.message",
			],
			[
				"displayName" => "访问日志",
				"page" => "page.manager.starter.log",
			],

		]
	],
	[
		"displayName" => "个人中心",
		"icon" => "UserOutlined",
		"key" => "user_center",
		"children" => [
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
		"icon" => "SettingOutlined",
		"key" => "system_management",
		"children" => [
			[
				"displayName" => "字典管理",
				"page" => "page.manager.starter.dict"
			],
			[
				'displayName' => '审核设置',
				'page' => 'page.manager.approval.process',
			],
			[
				"displayName" => "安全设置",
				"page" => "page.manager.configuration.security",
			],
		]
	]
];
