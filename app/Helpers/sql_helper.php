<?php


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
