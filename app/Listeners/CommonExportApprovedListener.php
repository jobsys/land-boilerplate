<?php

namespace App\Listeners;

use App\Events\CommonExportApproved;
use App\Messages\CommonExportApprovedMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonExportApprovedListener implements ShouldQueue
{
	/**
	 * Create the event listener.
	 */
	public function __construct()
	{
	}

	/**
	 * Handle the event.
	 * @throws \Exception
	 */
	public function handle(CommonExportApproved $event): void
	{
		$task = $event->task;
		$task->load('approvable', 'approvable.creator');
		$record = $task->approvable;
		$creator = $record->creator;

		$creator->sendMessage(new CommonExportApprovedMessage($record));
	}
}
