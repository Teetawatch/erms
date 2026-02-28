<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'เพิ่มความคิดเห็นเรียบร้อย');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success', 'ลบความคิดเห็นเรียบร้อย');
    }
}
