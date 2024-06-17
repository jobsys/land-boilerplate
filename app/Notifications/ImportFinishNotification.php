<?php

namespace App\Notifications;


use Illuminate\Notifications\Notification;
use Modules\ImportExport\Services\ImportExportService;

/***
 * 导入完成后的通知
 */
class ImportFinishNotification extends Notification
{

	public string $title;
	public string $import_id;

	public ImportExportService $service;

	public function __construct(string $title, string $import_id)
	{
		$this->title = $title;
		$this->import_id = $import_id;
		$this->service = app(ImportExportService::class);

	}

	public function via($notifiable): array
	{
		return ['database'];
	}

	public function toDatabase($notifiable): array
	{

		$progress = $this->service->getImportProgress($this->import_id);
		$success = $progress['total_rows'] - $progress['error_rows'];

		return [
			'url' => route('page.manager.tool.data-transfer'),
			'title' => $this->title,
			'message' => "导入数据完成: 共导入{$progress['total_rows']}条数据，成功{$success}条，失败{$progress['error_rows']}条"
		];
	}
}
