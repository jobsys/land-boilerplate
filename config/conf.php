<?php
return [
    //是否使用https
    'use_https' => env('USE_HTTPS', false),

    //超级管理员角色
    'super_role' => 'super-admin',
    //超级管理员账号
    'super_admin_name' => 'root',
    'super_admin_id' => 1,
    //超级管理员密码
    'super_password' => env('SUPER_PWD', ''),
    //登录字段
    'login_field' => ['name'],

    ######## 以下配置项为客户化配置项，需要在.env中配置 ########
    //默认客户标识
    'customer_identify' => env('CUSTOMER_IDENTIFY', 'default'),
];