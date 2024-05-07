<?php

namespace App\Exporters;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Starter\Entities\DictionaryItem;

class  DictExporter implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public int $dict_id;

    public function __construct($dict_id)
    {
        $this->dict_id = $dict_id;
    }

    public function query()
    {
        return DictionaryItem::where('dictionary_id', $this->dict_id);
    }

    public function headings(): array
    {
        return [
            '显示名称',
            '值',
            '是否启用',
            '排序',
        ];
    }

    public function map($row): array
    {
        return [
            $row->display_name,
            $row->value,
            $row->is_active ? '是' : '否',
            $row->sort_order,
        ];
    }
}
