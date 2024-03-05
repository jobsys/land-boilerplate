<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Permission\Traits\Authorisations;
use Modules\Starter\Entities\BaseClosureModel;

class Department extends BaseClosureModel
{
	use Authorisations;

	protected $casts = [
		'is_active' => 'boolean'
	];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class);
	}


	public $appends = [
		'model_type'
	];

	public function getModelTypeAttribute(): string
	{
		return 'department';
	}
}
