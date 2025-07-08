<?php
return [

	# 超级管理员角色
	'role_super' => '超级管理员',

	# 超级管理员账号
	'super_admin_name' => 'hyperuser',
	'super_admin_id' => 1,

	# 超级管理员密码
	'super_password' => env('SUPER_PWD', ''),

	# 登录字段, 支持 user 表中的字段作为检索字段
	'login_field' => ['name'],

	# 是否使用https
	'use_https' => env('USE_HTTPS', false),

	# sm2加密公钥
	'sm2_public_key' => env('SM2_PUBLIC_KEY'),
	# sm2加密私钥
	'sm2_private_key' => env('SM2_PRIVATE_KEY'),

	# 是否禁用上传
	'disabled_upload' => env('DISABLED_UPLOAD', false),

	# 是否启用CAS
	'use_cas' => env('USE_CAS', false),

	# 是否启用微信
	'use_wechat_channel' => env('USE_WECHAT_CHANNEL'),

	####### 业务配置 ########

	# 角色配置
	# 校级管理员
	# 'role_school_manager' => '校级管理员',

	# 部门配置
	# 系统管理部门
	'system_department_id' => 1,

    ######## 以下配置项为客户化配置项，需要在.env中配置 ########
	# 默认客户标识
	'customer_identify' => env('CUSTOMER_IDENTIFY', 'default'),
	'customer_name' => env('CUSTOMER_NAME', 'default'),


	####### 字典KEY ########
	# 'dict_nation_type' => 'nation_type', //民族
];
