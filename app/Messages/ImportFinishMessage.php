<?php

namespace App\Messages;


use Modules\Importexport\Supports\ImportProgressRecorder;
use Modules\Starter\Abstracts\Message;
use Modules\Starter\Enums\Message\MessageChannel;
use Modules\Starter\Enums\Message\MessageType;

/***
 * 导入完成后的通知
 */
class ImportFinishMessage extends Message
{

	public function __construct(public string $title, public string $task_id)
	{
	}

	public function via($receiver): array
	{
		return [MessageChannel::DATABASE];
	}

	public function messageBag($receiver): array
	{
		$progress = (new ImportProgressRecorder($this->task_id))->getProgress();
		$success = $progress['total'] - $progress['error'];

		return [
			'title' => $this->title,
			'content' => "导入数据完成: 共导入{$progress['total']}条数据，成功{$success}条，失败{$progress['error']}条",
			'type' => MessageType::NOTIFICATION,
			'data' => [
				'url' => route('page.manager.tool.data-transfer')
			]
		];
	}
}
