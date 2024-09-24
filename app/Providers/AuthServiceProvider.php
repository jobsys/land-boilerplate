<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The model to policy mappings for the application.
	 *
	 * @var array<class-string, class-string>
	 */
	protected $policies = [
		//
	];

	/**
	 * Register any authentication / authorization services.
	 */
	public function boot(): void
	{
		//超管权限
		Gate::before(function ($user, $ability) {
			return $user->hasRole(config('conf.role_super')) ? true : null;
		});

		//Log viewer 查看权限
		Gate::define('viewLogViewer', function (?User $user) {
			return $user && $user->id === config('conf.super_admin_id');
		});
	}
}
