<?php

namespace App\Messages;


use Modules\Approval\Enums\ApprovalStatus;
use Modules\Importexport\Entities\TransferRecord;
use Modules\Starter\Abstracts\Message;
use Modules\Starter\Enums\Message\MessageChannel;
use Modules\Starter\Enums\Message\MessageType;

/***
 * 导出任务审核通过后发送给创建人的通知
 */
class CommonExportApprovedMessage extends Message
{
	public function __construct(public TransferRecord $record)
	{
	}

	public function via($receiver): array
	{
		return [MessageChannel::DATABASE];
	}

	public function messageBag($receiver): array
	{
		return [
			'title' => '导出审核结果',
			'content' => $this->record->approval_status === ApprovalStatus::Approved ?
				"导出任务：{$this->record->task_name} 已经审核通过" :
				"导出任务：{$this->record->task_name} 审核被驳回， 驳回原因：{$this->record->approval_comment}",
			'type' => MessageType::NOTIFICATION,
		];
	}
}
