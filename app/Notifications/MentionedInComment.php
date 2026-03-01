<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionedInComment extends Notification
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
            'message' => "{$this->comment->user->name} กล่าวถึงคุณในงาน \"{$this->comment->task->title}\"",
            'task_id' => $this->comment->task_id,
            'comment_id' => $this->comment->id,
            'type' => 'mentioned_in_comment',
        ];
    }
}
