<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Approval\Entities\ApprovalTask;
use Modules\Importexport\Entities\TransferRecord;

/***
 * 通用导出审核待办
 */
class CommonExportApproveTodo extends Notification implements shouldQueue
{
	use Queueable;

	public ApprovalTask $task;
	public TransferRecord $record;

	public function __construct(ApprovalTask $task, TransferRecord $record)
	{
		$this->task = $task;
		$this->record = $record;
		$record->loadMissing('creator');
	}

	public function via($notifiable): array
	{
		return ['database'];
	}

	public function toDatabase($notifiable): array
	{
		return [
			'url' => route('page.manager.tool.data-transfer'),
			'title' => "导出待审核",
			'message' => "待审核项目： {$this->record->task_name}；申请人：{$this->record->creator?->name}"
		];
	}
}
