<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->comment->user->name} แสดงความคิดเห็นในงาน \"{$this->comment->task->title}\"",
            'task_id' => $this->comment->task_id,
            'comment_id' => $this->comment->id,
            'type' => 'new_comment',
        ];
    }
}
