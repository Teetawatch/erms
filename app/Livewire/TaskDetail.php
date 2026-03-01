<?php

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\TaskUpdate;
use Livewire\Component;

class TaskDetail extends Component
{
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

    protected $listeners = ['refreshTask' => '$refresh'];

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->progress = $task->progress;
    }

    public function addSubtask()
    {
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
        $subtask = Task::find($subtaskId);
        if ($subtask && $subtask->parent_id === $this->task->id) {
            $subtask->delete();
            $this->task->refresh();
        }
    }

    public function addDependency()
    {
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
        TaskDependency::where('id', $depId)->where('task_id', $this->task->id)->delete();
        $this->task->refresh();
    }

    public function updateProgress()
    {
        $this->validate(['progress' => 'required|integer|min:0|max:100']);
        $this->task->update(['progress' => $this->progress]);
        $this->editingProgress = false;
    }

    public function quickStatusChange($status)
    {
        $oldStatus = $this->task->status;
        $this->task->update(['status' => $status]);

        TaskUpdate::create([
            'task_id' => $this->task->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $status,
            'note' => null,
        ]);

        $this->task->refresh();
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
            'parent', 'customFieldValues.customField',
        ]);

        return view('livewire.task-detail');
    }
}
