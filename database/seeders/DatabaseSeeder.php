<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Permission\Database\Seeders\PermissionDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
		$identify = ucfirst(config('conf.customer_identify'));
		$this->call([
			SetupSeeder::class,
			PermissionDatabaseSeeder::class,
			"Database\\Seeders\\Dict{$identify}Seeder"
		]);
    }
}
