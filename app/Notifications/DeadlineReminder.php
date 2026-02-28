<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineReminder extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "งาน \"{$this->task->title}\" ใกล้ถึงกำหนดส่ง ({$this->task->due_date->translatedFormat('d M Y')})",
            'task_id' => $this->task->id,
            'type' => 'deadline_reminder',
        ];
    }
}
