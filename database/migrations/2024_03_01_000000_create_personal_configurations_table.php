<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		Schema::create('personal_configurations', function (Blueprint $table) {
			$table->id();
			$table->string('configurable_type')->index()->comment('个人类型');
			$table->integer('configurable_id')->index()->comment('个人ID');
			$table->string('key')->index()->comment('配置键');
			$table->json('value')->nullable()->comment('配置值');
			$table->timestamps();
			$table->comment('个人信息配置表');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('personal_configurations');
	}
};
