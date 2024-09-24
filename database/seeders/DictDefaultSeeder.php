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

		/**
		 * Demo 1
		 * */
		 /* if (!Dictionary::where('slug', config('conf.dict_key'))->exists()) {
			$dict = Dictionary::create(['name' => '词典名称', 'is_cascaded' => false, 'slug' => config('conf.dict_key')]);
			$items = ["A", "B", "C", "D"];
			foreach ($items as $index => $item) {
				DictionaryItem::updateOrCreate(['name' => $item, 'dictionary_id' => $dict->id], ["parent_id" => null, "value" => $item, 'sort_order' => $index * -1]);
			}
		}*/

		/**
		 * Demo 2
		 * */
		/*if (!Dictionary::where('slug', config('conf.dict_key'))->exists()) {
			$dict = Dictionary::create(['name' => '词典名称', 'is_cascaded' => false, 'slug' => config('conf.dict_key')]);

			$items = [
				['name' => 'A', 'value' => 'A'],
				['name' => 'B', 'value' => 'B'],
				['name' => 'C', 'value' => 'C'],
			];

			foreach ($items as $index => $item) {
				DictionaryItem::updateOrCreate(['name' => $item['name'], 'dictionary_id' => $dict->id], ["parent_id" => null, "value" => $item['value'], 'sort_order' => $index * -1]);
			}
		}*/

		/**
		 * 民族
		 */
		/*if (!Dictionary::where('slug', config('conf.dict_nation_type'))->exists()) {
			$dict = Dictionary::create(['name' => '民族', 'is_cascaded' => false, 'slug' => config('conf.dict_nation_type')]);

			$items = ["汉族", "蒙古族", "回族", "藏族", "维吾尔族", "苗族", "彝族", "壮族", "布依族", "朝鲜族", "满族", "侗族", "瑶族", "白族", "土家族", "哈尼族", "哈萨克族", "傣族", "黎族", "傈僳族", "佤族", "畲族", "高山族", "拉祜族", "水族", "东乡族", "纳西族", "景颇族", "柯尔克孜族", "土族", "达斡尔族",
				"仫佬族", "羌族", "布朗族", "撒拉族", "毛南族", "仡佬族", "锡伯族", "阿昌族", "普米族", "塔吉克族", "怒族", "乌孜别克族", "俄罗斯族", "鄂温克族", "德昂族", "保安族", "裕固族", "京族", "塔塔尔族", "独龙族", "鄂伦春族", "赫哲族", "门巴族", "珞巴族", "基诺族", "其他", "外国血统"];

			foreach ($items as $index => $item) {
				DictionaryItem::updateOrCreate(['name' => $item, 'dictionary_id' => $dict->id], ["parent_id" => null, "value" => $item, 'sort_order' => $index * -1]);
			}
		}*/


	}
}
