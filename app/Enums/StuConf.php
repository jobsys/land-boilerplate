<?php

namespace App\Enums;

/**
 * 存放学生配置的 key, 用于在学生表中存储配置信息
 * 而是在具体业务处理后将结果存储在 PersonalConfiguration 中，以便在其他地方查询
 */
enum StuConf
{
	const daily_functions = 'daily_functions'; //常用功能

}
