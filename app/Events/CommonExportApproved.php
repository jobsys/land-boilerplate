<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Approval\Entities\ApprovalTask;

class CommonExportApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ApprovalTask $task;

    /**
     * Create a new event instance.
     */
    public function __construct(ApprovalTask $task)
    {
        $this->task = $task;
    }
}
