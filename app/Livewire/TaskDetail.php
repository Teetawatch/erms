<?php

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\TaskUpdate;
use App\Models\TimeEntry;
use App\Notifications\TaskStatusChanged;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskDetail extends Component
{
    use WithFileUploads;

    public Task $task;

    // Subtask form
    public $showSubtaskForm = false;
    public $subtaskTitle = '';

    // Dependency form
    public $showDependencyForm = false;
    public $dependsOnTaskId = '';

    // Quick edit
    public $editingProgress = false;
    public $progress = 0;

    // Link attachment form
    public $showLinkForm = false;
    public $linkName = '';
    public $linkUrl = '';

    // Inline comment
    public $commentBody = '';
    public $isAnonymousComment = false;

    // Time tracking
    public $showTimeEntryForm = false;
    public $timeEntryHours = '';
    public $timeEntryDescription = '';
    public $timeEntryDate = '';

    // File upload
    public $uploadFiles = [];

    protected $listeners = ['refreshTask' => '$refresh'];

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->progress = $task->progress;
        $this->timeEntryDate = now()->format('Y-m-d');
    }

    public function canManageTask(): bool
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            || $user->hasRole('manager')
            || $this->task->assigned_to === $user->id;
    }

    public function addSubtask()
    {
        if (!$this->canManageTask()) return;
        $this->validate(['subtaskTitle' => 'required|string|max:255']);

        Task::create([
            'project_id' => $this->task->project_id,
            'parent_id' => $this->task->id,
            'title' => $this->subtaskTitle,
            'status' => 'todo',
            'priority' => $this->task->priority,
            'assigned_to' => $this->task->assigned_to,
            'sort_order' => $this->task->subtasks()->count(),
        ]);

        $this->subtaskTitle = '';
        $this->showSubtaskForm = false;
        $this->task->refresh();
    }

    public function toggleSubtask($subtaskId)
    {
        if (!$this->canManageTask()) return;
        $subtask = Task::find($subtaskId);
        if (!$subtask || $subtask->parent_id !== $this->task->id) return;

        $oldStatus = $subtask->status;
        $newStatus = $subtask->status === 'done' ? 'todo' : 'done';
        $subtask->update(['status' => $newStatus]);

        TaskUpdate::create([
            'task_id' => $subtask->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => null,
        ]);

        $this->task->refresh();
    }

    public function deleteSubtask($subtaskId)
    {
        if (!$this->canManageTask()) return;
        $subtask = Task::find($subtaskId);
        if ($subtask && $subtask->parent_id === $this->task->id) {
            $subtask->delete();
            $this->task->refresh();
        }
    }

    public function addDependency()
    {
        if (!$this->canManageTask()) return;
        $this->validate(['dependsOnTaskId' => 'required|exists:tasks,id']);

        if ($this->dependsOnTaskId == $this->task->id) {
            $this->addError('dependsOnTaskId', 'ไม่สามารถผูกกับงานเดียวกัน');
            return;
        }

        TaskDependency::firstOrCreate([
            'task_id' => $this->task->id,
            'depends_on_task_id' => $this->dependsOnTaskId,
        ], ['type' => 'finish_to_start']);

        $this->dependsOnTaskId = '';
        $this->showDependencyForm = false;
        $this->task->refresh();
    }

    public function removeDependency($depId)
    {
        if (!$this->canManageTask()) return;
        TaskDependency::where('id', $depId)->where('task_id', $this->task->id)->delete();
        $this->task->refresh();
    }

    public function updateProgress()
    {
        if (!$this->canManageTask()) return;
        $this->validate(['progress' => 'required|integer|min:0|max:100']);
        $this->task->update(['progress' => $this->progress]);
        $this->editingProgress = false;
    }

    public function quickStatusChange($status)
    {
        if (!$this->canManageTask()) return;
        $oldStatus = $this->task->status;
        $this->task->update(['status' => $status]);

        TaskUpdate::create([
            'task_id' => $this->task->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $status,
            'note' => null,
        ]);

        // Notify assigned user about status change
        if ($oldStatus !== $status && $this->task->assigned_to && $this->task->assigned_to !== auth()->id()) {
            $this->task->assignee?->notify(new TaskStatusChanged($this->task, $oldStatus, $status, auth()->user()->name));
        }

        $this->task->refresh();
    }

    public function addComment()
    {
        $this->validate(['commentBody' => 'required|string|max:2000']);

        $comment = $this->task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->commentBody,
            'is_anonymous' => $this->isAnonymousComment,
        ]);

        $comment->load(['user', 'task']);

        // Notify task assignee
        if ($this->task->assigned_to && $this->task->assigned_to !== auth()->id()) {
            $this->task->assignee->notify(new \App\Notifications\NewComment($comment));
        }

        // Notify @mentioned users
        preg_match_all('/@(\S+)/', $this->commentBody, $matches);
        if (!empty($matches[1])) {
            $mentionedUsers = \App\Models\User::whereIn('name', $matches[1])->get();
            foreach ($mentionedUsers as $mentioned) {
                if ($mentioned->id !== auth()->id()) {
                    $mentioned->notify(new \App\Notifications\MentionedInComment($comment));
                }
            }
        }

        $this->commentBody = '';
        $this->isAnonymousComment = false;
        $this->task->refresh();
        $this->dispatch('toast', message: 'เพิ่มความคิดเห็นเรียบร้อย', type: 'success');
    }

    public function deleteComment($commentId)
    {
        $comment = \App\Models\Comment::find($commentId);
        if ($comment && ($comment->user_id === auth()->id() || auth()->user()->hasRole('admin'))) {
            $comment->delete();
            $this->task->refresh();
            $this->dispatch('toast', message: 'ลบความคิดเห็นเรียบร้อย', type: 'success');
        }
    }

    public function uploadAttachments()
    {
        $this->validate([
            'uploadFiles.*' => 'file|max:10240',
        ]);

        foreach ($this->uploadFiles as $file) {
            $path = $file->store('attachments', 'public');
            $this->task->attachments()->create([
                'user_id' => auth()->id(),
                'type' => 'file',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
            ]);
        }

        $this->uploadFiles = [];
        $this->task->refresh();
        $this->dispatch('toast', message: 'อัปโหลดไฟล์สำเร็จ', type: 'success');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);
        if ($attachment && $attachment->task_id === $this->task->id) {
            if ($attachment->isFile() && $attachment->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
            $this->task->refresh();
            $this->dispatch('toast', message: 'ลบไฟล์สำเร็จ', type: 'success');
        }
    }

    public function addLinkAttachment()
    {
        $this->validate([
            'linkName' => 'required|string|max:255',
            'linkUrl' => 'required|url|max:2000',
        ], [
            'linkName.required' => 'กรุณากรอกชื่อลิงก์',
            'linkUrl.required' => 'กรุณากรอก URL',
            'linkUrl.url' => 'รูปแบบ URL ไม่ถูกต้อง',
        ]);

        Attachment::create([
            'task_id' => $this->task->id,
            'user_id' => auth()->id(),
            'type' => 'link',
            'file_name' => $this->linkName,
            'file_path' => '',
            'file_size' => 0,
            'external_url' => $this->linkUrl,
        ]);

        $this->linkName = '';
        $this->linkUrl = '';
        $this->showLinkForm = false;
        $this->task->refresh();

        $this->dispatch('toast', message: 'เพิ่มลิงก์สำเร็จ', type: 'success');
    }

    public function deleteLinkAttachment($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);
        if ($attachment && $attachment->task_id === $this->task->id && $attachment->isLink()) {
            $attachment->delete();
            $this->task->refresh();
            $this->dispatch('toast', message: 'ลบลิงก์สำเร็จ', type: 'success');
        }
    }

    public function addTimeEntry()
    {
        $this->validate([
            'timeEntryHours' => 'required|numeric|min:0.25|max:24',
            'timeEntryDescription' => 'nullable|string|max:500',
            'timeEntryDate' => 'required|date',
        ], [
            'timeEntryHours.required' => 'กรุณากรอกจำนวนชั่วโมง',
            'timeEntryHours.min' => 'ต้องบันทึกอย่างน้อย 0.25 ชั่วโมง',
            'timeEntryHours.max' => 'ไม่สามารถบันทึกเกิน 24 ชั่วโมง',
            'timeEntryDate.required' => 'กรุณาเลือกวันที่',
        ]);

        TimeEntry::create([
            'task_id' => $this->task->id,
            'user_id' => auth()->id(),
            'hours' => $this->timeEntryHours,
            'description' => $this->timeEntryDescription,
            'date_worked' => $this->timeEntryDate,
        ]);

        $this->timeEntryHours = '';
        $this->timeEntryDescription = '';
        $this->timeEntryDate = now()->format('Y-m-d');
        $this->showTimeEntryForm = false;
        $this->task->refresh();
        $this->dispatch('toast', message: 'บันทึกเวลาเรียบร้อย', type: 'success');
    }

    public function deleteTimeEntry($timeEntryId)
    {
        $entry = TimeEntry::find($timeEntryId);
        if ($entry && ($entry->user_id === auth()->id() || auth()->user()->hasRole('admin'))) {
            $entry->delete();
            $this->task->refresh();
            $this->dispatch('toast', message: 'ลบบันทึกเวลาเรียบร้อย', type: 'success');
        }
    }

    public function getAvailableTasksProperty()
    {
        return Task::where('project_id', $this->task->project_id)
            ->where('id', '!=', $this->task->id)
            ->whereNotIn('id', $this->task->dependencies()->pluck('depends_on_task_id'))
            ->select('id', 'title')
            ->get();
    }

    public function render()
    {
        $this->task->load([
            'project', 'assignee', 'comments.user', 'attachments.user',
            'taskUpdates.user', 'subtasks.assignee', 'dependencies.dependsOnTask',
            'parent', 'customFieldValues.customField', 'timeEntries.user',
        ]);

        return view('livewire.task-detail');
    }
}
