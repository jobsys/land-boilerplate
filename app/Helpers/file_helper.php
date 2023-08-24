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
