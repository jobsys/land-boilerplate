<?php
if (!function_exists('land_csv_to_string')) {
    /**
     * 在CSV中将数字转换成字符输出
     * @param $value
     * @return string
     */
    function land_csv_to_string($value): string
    {
        return '="' . $value . '"';
    }


}


if (!function_exists('land_csv_cell_break')) {
    /**
     * CSV单元格换行符
     * @return string
     */
    function land_csv_cell_break(): string
    {
        return "\n";
    }
}


if (!function_exists('land_is_image')) {
	/**
	 * 判断一个文件名是否是图片文件
	 * @param string|null $filename
	 * @return bool
	 */
	function land_is_image(string|null $filename): bool
	{
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
	}
}


if (!function_exists('land_add_file_suffix')) {
	/**
	 * 为文件名添加后缀
	 * @param string|null $filename
	 * @param string $suffix
	 * @return string
	 */
	function land_add_file_suffix(string|null $filename, string $suffix = '_thumb'): string
	{
		$path_info = pathinfo($filename);
		return $path_info['dirname'] . DIRECTORY_SEPARATOR . $path_info['filename'] . $suffix . '.' . $path_info['extension'];
	}
}
