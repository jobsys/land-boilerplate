<?php
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('land_get_nested_data')) {
	/**
	 * 获取给定ID的所有子级ID
	 * @param string $table  表名
	 * @param int $select_id 目标ID
	 * @param bool $include_self 是否包含自身
	 * @param string $id_key ID字段名
	 * @param string $parent_id_key 父ID字段名
	 * @return array
	 */
	function land_get_nested_data(string $table, int $select_id, bool $include_self = true, string $id_key = 'id', string $parent_id_key = 'parent_id'): array
	{
		//直接查询当前给定部门的子部门ID
		$query = "SELECT $id_key FROM " .
			" ( SELECT $id_key,$parent_id_key FROM $table ORDER BY $parent_id_key, $id_key ) $table, ( SELECT @pv := $select_id ) initialisation " .
			" WHERE find_in_set( $parent_id_key, @pv ) > 0 	AND @pv := concat(@pv,',',$id_key)";

		$items = DB::select($query);

		if ($include_self) {
			return collect($items)->pluck($id_key)->push($select_id)->toArray();
		} else {
			return collect($items)->pluck($id_key)->toArray();
		}
	}
}


if (!function_exists('land_is_model_unique')) {
	/**
	 * 判断模型中的某个参数在数据库中是否唯一
	 * @param array $data
	 * @param string $model
	 * @param string $key
	 * @param bool $strict
	 * @param array $conditions
	 * @return bool
	 */
	function land_is_model_unique(array $data, string $model, string $key, bool $strict = false, array $conditions = []): bool
	{
		if (!isset($data[$key]) || !$data[$key]) {
			return !$strict;
		}
		$query = app($model)->where($key, $data[$key]);

		if (!empty($conditions)) {
			foreach ($conditions as $a => $b) {
				$query->where($a, $b);
			}
		}

		if (isset($data['id']) && $data['id']) {
			$query->where('id', '<>', $data['id']);
		}

		return !$query->exists();
	}
}


if (!function_exists('land_filterable')) {
	/**
	 * 通用的查询过滤器，用于配合 NewbieSearch
	 * @param string $column
	 * @param Builder $builder
	 * @param array $query
	 * @return Builder
	 */
	function land_filterable(string $column, Builder $builder, array $query): Builder
	{
		switch ($query['type']) {
			case "input":
				$builder = match ($query['condition']) {
					'equal' => $builder->where($column, $query['value']),
					'notEqual' => $builder->where($column, '!=', $query['value']),
					'include' => $builder->where($column, 'like', "%{$query['value']}%"),
					'exclude' => $builder->where($column, 'not like', "%{$query['value']}%"),
					'null' => $builder->whereNull($column),
					'notNull' => $builder->whereNotNull($column),
				};
				break;
			case "select":
				$builder = match ($query['condition']) {
					'equal' => $builder->where($column, $query['value']),
					'notEqual' => $builder->where($column, '!=', $query['value']),
					'include' => $builder->whereIn($column, $query['value']),
					'exclude' => $builder->whereNotIn($column, $query['value']),
				};
				break;
			case "number":
				$builder = match ($query['condition']) {
					'equal' => $builder->where($column, $query['value']),
					'notEqual' => $builder->where($column, '!=', $query['value']),
					'lessThan' => $builder->where($column, '<', $query['value']),
					'greaterThan' => $builder->where($column, '>', $query['value']),
					'between' => $builder->whereBetween($column, $query['value']),
				};
				break;
			case "date":

				$query['value'] = is_array($query['value']) ? collect($query['value'])->map(fn($v) => land_predict_date_time($v, 'date'))->toArray() : land_predict_date_time($query['value'], 'date');

				$builder = match ($query['condition']) {
					'equal' => $builder->whereDate($column, $query['value']),
					'lessThan' => $builder->whereDate($column, '<', $query['value']),
					'greaterThan' => $builder->whereDate($column, '>', $query['value']),
					'between' => $builder->whereBetween($column, CarbonPeriod::between($query['value'][0], $query['value'][1]))
				};
				break;
			case "textarea":
				$builder = match ($query['condition']) {
					'equal' => $builder->whereIn($column, $query['value']),
					'exclude' => $builder->whereNotIn($column, $query['value'])
				};
				break;
			default:
				break;
		}

		return $builder;
	}
}
