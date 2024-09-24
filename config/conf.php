<?php
return [
	//是否使用https
	'use_https' => env('USE_HTTPS', false),

	//超级管理员角色
	'role_super' => '超级管理员',
	//超级管理员账号
	'super_admin_name' => 'root',
	'super_admin_id' => 1,
	//超级管理员密码
	'super_password' => env('SUPER_PWD', ''),
	//登录字段
	'login_field' => ['name'],

	//####### 业务配置 ########
	//系统管理部门
	'system_department_id' => 1,
	//是否启用微信
	'wechat_channel' => env('WECHAT_CHANNEL'),

    ######## 以下配置项为客户化配置项，需要在.env中配置 ########
	//默认客户标识
	'customer_identify' => env('CUSTOMER_IDENTIFY', 'default'),
	'customer_name' => env('CUSTOMER_NAME', 'default'),
];
