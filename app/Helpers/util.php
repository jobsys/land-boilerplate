<?php

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use OneSm\Sm3;


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
			if ($item->$parent_key === $target) {
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
	 * @return array
	 */
	function land_get_closure_tree($items, string $children_key = 'children'): array
	{
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

		function recursive_sort($items, $children_key)
		{
			return $items->map(function (Model $item) use ($children_key) {
				if (!count($item->{$children_key})) {
					unset($item->{$children_key});
					return $item->toArray();
				}
				$children = recursive_sort($item->{$children_key}, $children_key)->sortByDesc('sort_order')->values()->toArray();
				if ($item->isRelation($children_key)) {
					$item->unsetRelation($children_key);
				}
				$item->{$children_key} = $children;
				return $item->toArray();
			});
		}

		$items = recursive_sort($items, $children_key)->sortByDesc('sort_order')->values()->toArray();

		return $single ? $items[0] : $items;
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
				$tree[$index] = array_merge($callback($item), [$children_key => $children]);
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
	function land_recursive_accumulate(array|Collection $data, array|string $accumulate_fields, string $children_key = 'children'): void
	{
		$accumulate_fields = is_array($accumulate_fields) ? $accumulate_fields : [$accumulate_fields];

		foreach ($data as &$item) {

			// 根据 $accumulate_fields 累加当前级别各个 Field 的总和

			if (!empty($item[$children_key])) {
				$children = $item[$children_key];

				land_recursive_accumulate($children, $accumulate_fields, $children_key);

				foreach ($accumulate_fields as $field) {
					$item[$field] = is_array($item[$children_key])
						? array_reduce($item[$children_key], function ($carry, $child) use ($field) {
							return $carry + $child[$field];
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
		if (!isset($type) || $type === 'dateTime' || $type === 'date') {
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
	 * 调用客户定制的方法
	 * @param $class
	 * @param $func
	 * @param array $params
	 * @return mixed
	 */
	function land_customer_func($class, $func, array $params = []): mixed
	{
		$customer_identify = config('conf.customer_identify', 'default');
		if ($customer_identify !== 'default') {
			$func = $customer_identify . ucfirst($func);
		}

		return call_user_func_array([$class, $func], $params);
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

if (!function_exists('land_sm3')) {
	/**
	 * 国密 SM3 签名
	 * @param $content
	 * @return string
	 */
	function land_sm3($content): string
	{
		$sm = new Sm3();
		return $sm->sign($content);
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
