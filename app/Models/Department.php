<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Permission\Traits\Authorisations;
use Modules\Starter\Entities\BaseModel;

class Department extends BaseModel
{
	use Authorisations, NodeTrait;


	protected $model_name = "部门";

	protected $casts = [
		'is_active' => 'boolean',
	];

	protected $hidden = [
		'_lft',
		'_rgt',
		'updated_at',
		'created_at',
	];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class);
	}

	public $appends = [
		'model_type',
	];

	public function getModelTypeAttribute(): string
	{
		return 'department';
	}
}
