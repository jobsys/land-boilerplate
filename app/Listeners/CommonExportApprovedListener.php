<?php

namespace App\Listeners;

use App\Events\CommonExportApproved;
use App\Notifications\CommonExportApprovedNotification;
use Modules\Approval\Services\ApprovalService;

class CommonExportApprovedListener
{
    /**
     * Create the event listener.
     */

    protected ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
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

        $creator->notify(new CommonExportApprovedNotification($record));
    }
}
