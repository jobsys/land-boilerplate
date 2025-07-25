<?php

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Rtgm\sm\RtSm2;
use Rtgm\sm\RtSm3;


if (!function_exists('land_classify')) {
	/**
	 * 将列表进行父子级分类
	 * @param $items
	 * @param string $parent_key
	 * @param int $target
	 * @param string $children_key
	 * @param callable|null $callback 支持对每个 item 进行回调处理
	 * @return Collection
	 */
	function land_classify($items, string $parent_key = 'parent_id', int $target = 0, string $children_key = 'children', callable $callback = null): Collection
	{

		$items = $items instanceof Collection ? $items : collect($items);

		$result = collect();

		foreach ($items as $index => $item) {
			if ($item->$parent_key === $target || ($target === 0 && !$item->$parent_key)) {
				$items->forget($index);
				$children = land_classify($items, $parent_key, $item->id, $children_key, $callback);
				if ($children->count()) {
					$item->{$children_key} = $children;
				}
				if ($callback) {
					$item = $callback($item);
				}
				$result->push($item);
			}
		}

		if ($target === 0) {
			foreach ($items as $item) {
				$pushed_item = land_classify($item, $parent_key, $item->id, $children_key, $callback)->first();
				if ($callback) {
					$pushed_item = $callback($callback);
				}
				$result->push($pushed_item);
			}
		}

		return $result;
	}
}


if (!function_exists('land_get_closure_tree')) {
	/**
	 * 根据顶层 item 获取所有的嵌套 children
	 * @param $items
	 * @param string $children_key
	 * @param string|int $sort_direction 排序方向 desc|asc
	 * @return array
	 */
	function land_get_closure_tree($items, string $children_key = 'children', string|int $sort_direction = 'desc'): array
	{

		$sort_direction = ($sort_direction === 'asc' || $sort_direction === SORT_ASC) ? SORT_ASC : SORT_DESC;

		$single = false;

		if (!($items instanceof Collection)) {
			$items = collect($items);
			$single = true;
		}

		$items = $items->map(function ($item) use ($children_key) {
			$children = $item->getDescendants()->toTree();
			$item->{$children_key} = $children;
			return $item;
		});

		/**
		 * @var Collection $items
		 */
		$items = recursive_sort($items, $children_key, $sort_direction);
		if ($sort_direction === SORT_ASC) {
			$items = $items->sortBy('sort_order')->values()->toArray();
		} else {
			$items = $items->sortByDesc('sort_order')->values()->toArray();
		}
		return $single ? $items[0] : $items;
	}

	function recursive_sort($items, $children_key, $sort_direction)
	{
		return $items->map(function (Model $item) use ($children_key, $sort_direction) {
			if (!count($item->{$children_key})) {
				unset($item->{$children_key});
				return $item->toArray();
			}
			$children = recursive_sort($item->{$children_key}, $children_key, $sort_direction);

			if ($sort_direction === SORT_ASC) {
				$children = $children->sortBy('sort_order')->values()->toArray();
			} else {
				$children = $children->sortByDesc('sort_order')->values()->toArray();
			}

			if ($item->isRelation($children_key)) {
				$item->unsetRelation($children_key);
			}
			$item->{$children_key} = $children;
			return $item->toArray();
		});
	}
}

if (!function_exists('land_tidy_tree')) {
	/**
	 * 对树形结构进行整理
	 * @param array $tree
	 * @param callable $callback
	 * @param string $children_key
	 * @return array
	 */
	function land_tidy_tree(array $tree, callable $callback, string $children_key = 'children'): array
	{
		foreach ($tree as $index => $item) {
			if (isset($item[$children_key]) && count($item[$children_key])) {
				$children = land_tidy_tree($item[$children_key], $callback, $children_key);
				$tree[$index] = $callback(array_merge($item, [$children_key => $children]));
			} else {
				$tree[$index] = $callback($item);
			}
		}
		return $tree;
	}
}

if (!function_exists('land_recursive_accumulate')) {
	/**
	 * 将子级字段递归累加到父级字段
	 * @param array|Collection $data
	 * @param array|string $accumulate_fields 累加字段，支持数组
	 * @param string $children_key
	 * @return void
	 */
	function land_recursive_accumulate(array|Collection &$data, array|string $accumulate_fields, string $children_key = 'children'): void
	{
		$accumulate_fields = is_array($accumulate_fields) ? $accumulate_fields : [$accumulate_fields];

		foreach ($data as &$item) {

			// 根据 $accumulate_fields 累加当前级别各个 Field 的总和

			if (!empty($item[$children_key])) {
				$children = $item[$children_key];

				land_recursive_accumulate($children, $accumulate_fields, $children_key);

				foreach ($accumulate_fields as $field) {
					$item[$field] = is_array($item[$children_key]) && !empty($item[$children_key])
						? array_reduce($item[$children_key], function ($carry, $child) use ($field) {
							return $carry + ($child[$field] ?? 0);
						}, $item[$field])
						: $item[$children_key]->sum($field);
				}
			}
		}
	}
}

if (!function_exists('land_request_ip')) {
	/**
	 * 获取请求IP
	 * @return array|string|null
	 */
	function land_request_ip(): array|string|null
	{
		return Request::header('x-forwarded-for') ?: Request::ip();
	}
}

if (!function_exists('land_predict_date_time_format')) {
	/**
	 * 预测日期字符的 Format 表达式
	 * @param $target
	 * @param string|null $type 'date|time|dateTime'
	 * @return string
	 */
	function land_predict_date_time_format($target, ?string $type): string
	{

		if (!$target) {
			return '';
		}

		$date_format = '';
		$time_format = '';
		if (!isset($type) || $type === 'dateTime' || $type === 'datetime' || $type === 'date') {
			if (Str::contains($target, ' ')) {
				list($date_part, $time_part) = explode(' ', $target);
			} else {
				$date_part = $target;
			}
			$split = '';
			if (Str::contains($date_part, '-')) {
				$split = '-';
			} else if (Str::contains($date_part, '/')) {
				$split = '/';
			}

			if (!$split) {
				if (Str::length($date_part) === 5 || Str::length($date_part === 6)) {
					$date_format = "Ym";
				} else if (Str::length($date_part) === 8) {
					$date_format = "Ymd";
				}
			} else {
				$blocks = explode($split, $date_part);
				if (count($blocks) === 2) {
					$date_format = "Y{$split}m";
				} else if (count($blocks) === 3) {
					$date_format = "Y{$split}m{$split}d";
				}
			}
		}

		if (($type === 'time' || isset($time_part)) && $type !== 'date') {
			$time_part = $time_part ?? $target;
			$time_blocks = explode(':', $time_part);
			if (isset($time_blocks[0])) {
				$time_format = "H";
			}
			if (isset($time_blocks[1])) {
				$time_format .= $time_format ? ":i" : "i";
			}
			if (isset($time_blocks[2])) {
				$time_format .= $time_format ? ":s" : "s";
			}

			return trim($date_format . ' ' . $time_format);
		}

		return trim($date_format);
	}
}

if (!function_exists('land_predict_date_time')) {
	/**
	 * 预测所给字符串的 Carbon 日期对象
	 * @param $target
	 * @param string $type 'date|dateTime'
	 * @return \Carbon\Carbon|bool|null
	 */
	function land_predict_date_time($target, string $type): \Carbon\Carbon|bool|null
	{
		if ($type === 'date' && Str::contains($target, ' ')) {
			$target = explode(' ', $target)[0];
		}
		$format = land_predict_date_time_format($target, $type);
		return $format ? ($type === 'date' ? Carbon::createFromFormat($format, $target)->startOfDay() : Carbon::createFromFormat($format, $target)) : null;
	}
}

if (!function_exists('land_config')) {
	/**
	 * Get / set the specified configuration value.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param array|string|null $key
	 * @param mixed|null $default
	 * @return mixed|Repository
	 */
	function land_config(array|string $key = null, mixed $default = null): mixed
	{
		$customer_identify = config("conf.customer_identify", "default");

		if (config()->has("{$customer_identify}.{$key}")) {
			return config("{$customer_identify}.{$key}");
		} else {
			return config("default.{$key}", $default);
		}
	}
}

if (!function_exists('land_customer_func')) {

	/**
	 * 调用客户定制的方法, 优先调用客户定制的方法，其次调用默认方法
	 * 例如: 调用 CustomerService 的 customer_func 方法，优先调用 CustomerService 的 customer_func 方法，其次调用 CustomerService 的 func 方法
	 * @param $class
	 * @param $func
	 * @param array $params
	 * @return mixed
	 * @throws Exception
	 */
	function land_customer_func($class, $func, array $params = []): mixed
	{
		$customer_identify = config('conf.customer_identify', 'default');
		if ($customer_identify !== 'default') {
			$customer_func = $customer_identify . ucfirst($func);
		}

		$class = app($class);

		if (isset($customer_func) && method_exists($class, $customer_func)) {
			return $class->$customer_func(...$params);
		} else if (method_exists($class, $func)) {
			return $class->$func(...$params);
		}
		throw new Exception("$func not exists in $class");
	}
}

if (!function_exists('land_form_validate')) {
	/**
	 * 表单验证
	 * @param array $input
	 * @param array $rules
	 * @param array $mapping
	 * @return array
	 */
	function land_form_validate(array $input = [], array $rules = [], array $mapping = []): array
	{
		$error = null;

		$validator = validator()->make($input, $rules)->setAttributeNames($mapping);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
		}

		if ($error === 'validation.captcha_api') {
			$error = '验证码不正确';
		}

		return [$input, $error];
	}
}

if (!function_exists('land_sm2_encrypt')) {
	/**
	 * 国密 SM2 加密
	 * @param $content
	 * @return string
	 */
	function land_sm2_encrypt($content): string
	{
		$sm2 = new RtSm2();
		return $sm2->doEncrypt($content, config('conf.sm2_public_key'));
	}
}

if (!function_exists('land_sm2_decrypt')) {
	/**
	 * 国密 SM2 解密
	 * @param $content
	 * @return string
	 */
	function land_sm2_decrypt($content): string
	{
		$sm2 = new RtSm2();
		$trim = strlen($content) !== 256;
		return $sm2->doDecrypt($content, config('conf.sm2_private_key'), $trim);
	}
}


if (!function_exists('land_sm3')) {
	/**
	 * 国密 SM3 签名
	 * @param $content
	 * @return string
	 */
	function land_sm3($content): string
	{
		$sm3 = new RtSm3();
		return $sm3->digest($content);
	}
}

if (!function_exists('land_fake_phone')) {
	/**
	 * 手机号码脱敏
	 * @param string $phone
	 * @return string
	 */
	function land_fake_phone(string $phone): string
	{
		return substr($phone, 0, 3) . '****' . substr($phone, -4);
	}
}

if (!function_exists('land_slug')) {
	/**
	 * 根据给定的字符串生成唯一的 slug
	 * @param $prop
	 * @param $model
	 * @param array $condition
	 * @param string $filed
	 * @return string
	 */
	function land_slug($prop, $model, array $condition = [], string $filed = 'slug'): string
	{
		$slug = mb_strlen($prop) > 10 ? pinyin_abbr($prop) : pinyin_permalink($prop);
		if ($slug instanceof \Overtrue\Pinyin\Collection) {
			$slug = $slug->join("");
		}
		$slug = strtolower($slug);

		$index = 1;
		while (!land_is_model_unique([$filed => $slug], $model, $filed, true, $condition)) {
			$slug = $slug . $index;
			$index += 1;
		}

		return $slug;
	}
}

if (!function_exists('land_split_by_line')) {
	/**
	 * 按行分割
	 * @param string $text
	 * @return array
	 */
	function land_split_by_line(string $text): array
	{
		return preg_split('/\r\n|\r|\n/', $text);
	}
}


if (!function_exists('land_get_newbie_query')) {
	/**
	 * 获取 NewbieQuery 的查询值
	 * @param string|null $key
	 * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\Request|mixed|string|null
	 */
	function land_get_newbie_query(string $key = null): mixed
	{

		$newbie_query = request()->exists('newbieQuery') ? request('newbieQuery') : request('_q', []);

		if (!$key) {
			return $newbie_query;
		}

		if (isset($newbie_query[$key])) {
			return $newbie_query[$key];
		}

		return null;
	}
}

if (!function_exists('land_area_code_restore')) {
	/**
	 * 恢复地区代码层级
	 * @param $area_code
	 * @return array
	 */
	function land_area_code_restore($area_code): array
	{
		if (Str::endsWith($area_code, '0000')) {
			return [$area_code];
		}

		if (Str::endsWith($area_code, '00')) {
			return [substr($area_code, 0, 2) . '0000', $area_code];
		}

		return [substr($area_code, 0, 2) . '0000', substr($area_code, 0, 4) . '00', $area_code];
	}
}


if (!function_exists('land_version_inertia_view')) {
	/**
	 * 根据版本号返回对应的 inertia 视图名称
	 * @param string $view_dir
	 * @param string $view_name
	 * @param string|null $version
	 * @return string
	 */
	function land_version_inertia_view(string $view_dir, string $view_name, string|null $version): string
	{
		$version_view = "{$view_dir}/{$view_name}.{$version}.vue";

		if (file_exists(resource_path("views/{$version_view}"))) {
			return "{$view_name}.{$version}";
		}
		return $view_name;
	}
}


if (!function_exists('land_write_env_file')) {
	/**
	 * 更新 .env 变量
	 * @param $key
	 * @param $value
	 * @return void
	 */
	function land_write_env_file($key, $value): void
	{
		$envPath = base_path('.env');

		if (file_exists($envPath)) {
			$env_content = file_get_contents($envPath);

			// 判断是否已经存在该变量
			if (preg_match("/^{$key}=.*/m", $env_content)) {
				// 替换已有值
				$env_content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $env_content);
			} else {
				// 追加新变量
				$env_content .= "\n{$key}=\"{$value}\"";
			}

			file_put_contents($envPath, $env_content);
		}
	}
}

if (!function_exists('land_mini_pic_placeholder')) {

	/**
	 * 生成小程序图片占位符
	 **/
	function land_mini_pic_placeholder(): string
	{
		return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==";
	}
}

if (!function_exists('land_cron_matches_now')) {
	/**
	 * 检查是否匹配 Cron 表达式
	 * @param ?string $cron
	 * @param string $wildcard
	 * @return bool
	 */
	function land_cron_matches_now(?string $cron, string $wildcard = "*"): bool
	{
		if (!$cron) {
			return false;
		}

		$matchesField = function (string $expr, int $value) use ($wildcard): bool {
			return $expr === $wildcard || (string)(int)$expr === (string)$value;
		};

		[$min, $hour, $day, $month, $week] = explode(' ', $cron);

		$now = now();

		// Carbon: 0（周日）~ 6（周六） → 我们要转换成 1（周一）~ 7（周日）
		$weekday = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek;

		return $matchesField($min, $now->minute)
			&& $matchesField($hour, $now->hour)
			&& $matchesField($day, $now->day)
			&& $matchesField($month, $now->month)
			&& $matchesField($week, $weekday);
	}
}
