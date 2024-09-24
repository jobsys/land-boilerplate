<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SnsUser extends Model
{

	protected $casts = [
		'bound_at' => 'datetime'
	];


	public function snsable(): MorphTo
	{
		return $this->morphTo();
	}
}
