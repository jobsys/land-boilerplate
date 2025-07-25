<?php

namespace App\Jobs;

use App\Messages\ImportFinishMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserOfCompletedImport implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public function __construct(public string $title, public string $task_id, public User $user)
	{
	}

	public function handle(): void
	{
		$this->user->sendMessage(new ImportFinishMessage($this->title, $this->task_id));
	}
}
