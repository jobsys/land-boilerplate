<?php


namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Starter\Entities\Dictionary;
use Modules\Starter\Entities\DictionaryItem;

class DictDefaultSeeder extends Seeder
{

	/**
	 * php artisan migrate --seed --seeder=Database\Seeders\DictSeeder
	 */


	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		Model::unguard();

		if (!Dictionary::where('slug', config('conf.maintenance_area_slug'))->exists()) {
			Dictionary::create(['id' => 1, 'name' => '维修区域', 'slug' => config('conf.maintenance_area_slug')]);
			$maintenance_areas = [
				[
					"id" => 300,
					"dictionary_id" => 1,
					"name" => "宿舍区域",
					"parent_id" => null,
					"value" => "宿舍区域"
				],
			];

			foreach ($maintenance_areas as $maintenance_area) {
				DictionaryItem::create($maintenance_area);
			}
		}
	}
}
