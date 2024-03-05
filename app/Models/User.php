<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Permission\Traits\Authorisations;
use Modules\Starter\Traits\Filterable;
use Modules\Starter\Traits\MessageReceiver;
use Modules\Starter\Traits\Accessable;
use Modules\Starter\Traits\Snsable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, HasRoles, Authorisations, Filterable, MessageReceiver, Snsable, Accessable;

	protected $guarded = [];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'password_salt',
		'remember_token',
	];


	public $appends = [
		'model_type'
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'avatar' => 'array',
		'email_verified_at' => 'datetime',
		'sms_verified_at' => 'datetime',
		'last_login_error_at' => 'datetime',
		'last_login_at' => 'datetime',
		'last_password_modify_at' => 'datetime',
		'is_active' => 'boolean',
	];

	public function departments(): BelongsToMany
	{
		return $this->belongsToMany(Department::class);
	}

	public function isSuperAdmin(): bool
	{
		return session('user_role') === config('conf.super_role', 'super-admin');
	}


	public function getModelTypeAttribute(): string
	{
		return 'user';
	}
}
