<?php

namespace App\Importers;

use Modules\Importexport\Importers\CollectionImporter;

class DemoImporter extends CollectionImporter
{
    public function store(array $row, array $extra): array
    {
        // write your stuff here

		return [];
    }
}
