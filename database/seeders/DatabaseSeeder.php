<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Database\Seeders\PermissionDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

		$seeders = [
			SetupSeeder::class,
			PermissionDatabaseSeeder::class,
		];

		$identify = ucfirst(config('conf.customer_identify'));

		if (file_exists(base_path("database/seeders/Dict{$identify}Seeder.php"))) {
			$seeders[] = "Database\\Seeders\\Dict{$identify}Seeder";
		} else {
			$seeders[] = DictDefaultSeeder::class;
		}

		$this->call($seeders);
    }
}
