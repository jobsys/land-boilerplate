<?php

namespace App\Providers;

use App\Events\CommonExportApproved;
use App\Listeners\CommonExportApprovedListener;
use App\Listeners\UserEventListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Approval\Events\ApprovableCreated;
use Modules\Approval\Listeners\ApprovableCreatedListener;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event to listener mappings for the application.
	 *
	 * @var array<class-string, array<int, class-string>>
	 */
	protected $listen = [
		Login::class => [
			[UserEventListener::class, 'handleUserLogin']
		],
		CommonExportApproved::class => [
			[CommonExportApprovedListener::class, 'handle']
		],
	];

	/**
	 * Register any events for your application.
	 */
	public function boot(): void
	{
		//
	}

	/**
	 * Determine if events and listeners should be automatically discovered.
	 */
	public function shouldDiscoverEvents(): bool
	{
		return false;
	}
}
