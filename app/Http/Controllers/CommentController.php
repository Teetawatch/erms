<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInComment;
use App\Notifications\NewComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $comment->load(['user', 'task']);

        // Notify task assignee (if not the commenter)
        if ($task->assigned_to && $task->assigned_to !== $request->user()->id) {
            $task->assignee->notify(new NewComment($comment));
        }

        // Notify @mentioned users
        preg_match_all('/@(\S+)/', $validated['body'], $matches);
        if (!empty($matches[1])) {
            $mentionedUsers = User::whereIn('name', $matches[1])->get();
            foreach ($mentionedUsers as $mentioned) {
                if ($mentioned->id !== $request->user()->id) {
                    $mentioned->notify(new MentionedInComment($comment));
                }
            }
        }

        return back()->with('success', 'เพิ่มความคิดเห็นเรียบร้อย');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success', 'ลบความคิดเห็นเรียบร้อย');
    }
}
