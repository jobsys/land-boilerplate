<?php

namespace App\Notifications;


use Illuminate\Notifications\Notification;
use Modules\Approval\Enums\ApprovalStatus;
use Modules\Importexport\Entities\TransferRecord;

/***
 * 导出任务审核通过后发送给创建人的通知
 */
class CommonExportApprovedNotification extends Notification
{

	public TransferRecord $record;

	public function __construct(TransferRecord $record)
	{
		$this->record = $record;
	}

	public function via($notifiable): array
	{
		return ['database'];
	}

	public function toDatabase($notifiable): array
	{
		return [
			'title' => '导出审批结果',
			'message' => $this->record->approval_status === ApprovalStatus::Approved ?
				"导出任务：{$this->record->task_name} 已经审批通过" :
				"导出任务：{$this->record->task_name} 审批被驳回， 驳回原因：{$this->record->approval_comment}"
		];
	}
}
