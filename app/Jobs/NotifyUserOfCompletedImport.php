<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ImportFinishNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyUserOfCompletedImport implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $title;
    public string $import_id;

    public function __construct(string $title, string $import_id, User $user)
    {
        $this->user = $user;
        $this->title = $title;
        $this->import_id = $import_id;
    }

    public function handle(): void
    {
        $this->user->notify(new ImportFinishNotification($this->title, $this->import_id));
    }
}
