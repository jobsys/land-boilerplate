<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Permission\Traits\Authorisations;
use Modules\Permission\Traits\HasDataScopes;
use Modules\Starter\Traits\Accessable;
use Modules\Starter\Traits\Filterable;
use Modules\Starter\Traits\MessageReceiver;
use Modules\Starter\Traits\Paginatable;
use Modules\Starter\Traits\Snsable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class User extends Authenticatable
{
	use HasDataScopes, Accessable, Authorisations, Filterable, HasApiTokens, HasFactory, HasRoles, MessageReceiver, Notifiable, Paginatable, Snsable, LogsActivity, HasJsonRelationships;

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
		'model_type',
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


	protected array $accessors = [
		'avatar' => 'file'
	];

	public static function getModelName(): string
	{
		return '用户';
	}


	public function departments(): BelongsToMany
	{
		return $this->belongsToMany(Department::class);
	}

	public function configurations(): MorphMany
	{
		return $this->morphMany(PersonalConfiguration::class, 'configurable');
	}

	public function isSuperAdmin(): bool
	{
		return $this->hasRole(config('conf.role_super'));
	}

	public function getModelTypeAttribute(): string
	{
		return 'user';
	}

	public function getActivitylogOptions(): LogOptions
	{
		return LogOptions::defaults()->setDescriptionForEvent(function (string $event_name) {
			return match ($event_name) {
				'created' => '创建用户',
				'updated' => '更新用户',
				'deleted' => '删除用户',
				default => ''
			};
		});
	}
}
