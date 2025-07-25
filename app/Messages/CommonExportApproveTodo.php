<?php

namespace App\Messages;


use Modules\Approval\Entities\ApprovalTask;
use Modules\Importexport\Entities\TransferRecord;
use Modules\Starter\Abstracts\Message;
use Modules\Starter\Enums\Message\MessageChannel;
use Modules\Starter\Enums\Message\MessageType;

/***
 * 通用导出审核待办
 */
class CommonExportApproveTodo extends Message
{
	public function __construct(public ApprovalTask $task, public TransferRecord $record)
	{

	}

	public function via($receiver): array
	{
		return [MessageChannel::DATABASE];
	}

	public function messageBag($receiver): array
	{
		$this->record->loadMissing('creator');
		return [
			'title' => "导出待审核",
			'content' => "待审核项目： {$this->record->task_name}；申请人：{$this->record->creator?->name}",
			'type' => MessageType::TODO,
			'data' => [
				'url' => route('page.manager.tool.data-transfer')
			],
		];
	}
}
