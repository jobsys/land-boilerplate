<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use Inertia\Inertia;
use Modules\Starter\Entities\Configuration;

class ConfigurationController extends BaseManagerController
{
	public function pageConfiguration()
	{
		$module = request('module', 'system');
		$configurations = Configuration::where('module', $module)->get();
		return Inertia::render('PageConfiguration', [
			'configurations' => $configurations
		]);
	}

	public function edit()
	{
		list($input, $error) = land_form_validate(
			request()->only(['configurations']),
			[
				'configurations' => 'bail|required|array'
			],
			[
				'configurations' => '配置项',
			],
		);

		if ($error) {
			return $this->message($error);
		}

		foreach ($input['configurations'] as $configuration) {
			Configuration::updateOrCreate([
				'module' => $configuration['module'],
				'group' => $configuration['group'],
				'name' => $configuration['name'],
			], $configuration);
		}

		log_access("编辑{$input['configurations'][0]['module']}配置项");

		return $this->json();

	}
}
