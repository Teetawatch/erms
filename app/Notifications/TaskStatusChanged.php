<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus,
        public string $changedByName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'todo' => 'รอดำเนินการ',
            'in_progress' => 'กำลังดำเนินการ',
            'review' => 'ตรวจสอบ',
            'done' => 'เสร็จสิ้น',
        ];

        return [
            'message' => "{$this->changedByName} เปลี่ยนสถานะงาน \"{$this->task->title}\" เป็น " . ($statusLabels[$this->newStatus] ?? $this->newStatus),
            'task_id' => $this->task->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'type' => 'task_status_changed',
        ];
    }
}
