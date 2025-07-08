<?php

namespace App\Enums;

/**
 * 存放用户配置的 key
 * 而是在具体业务处理后将结果存储在 PersonalConfiguration 中，以便在其他地方查询
 */
enum UserConf
{
	const daily_functions = 'daily_functions'; //常用功能
}
