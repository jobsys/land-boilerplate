<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Starter\Entities\BaseModel;

class PersonalConfiguration extends BaseModel
{

	protected $model_name = '个人配置';

	protected $casts = [
		'value' => 'array'
	];

	public function configurable(): MorphTo
	{
		return $this->morphTo();
	}
}
