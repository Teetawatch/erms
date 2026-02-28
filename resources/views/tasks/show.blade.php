<x-app-layout>
    <x-slot name="header">{{ $task->title }}</x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-erms-green/15 border border-erms-green/20 rounded-lg text-sm text-erms-green">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Info --}}
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-3">
                    @php
                        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
                        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
                    @endphp
                    <span class="badge-{{ str_replace('_', '-', $task->status) }}">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                    <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                    @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="text-xs text-erms-blue hover:underline">{{ $task->project->name }}</a>
                    @endif
                </div>
                @if($task->description)
                    <p class="text-sm text-erms-muted mb-4">{{ $task->description }}</p>
                @endif
                <div class="flex items-center gap-6 text-xs text-erms-muted">
                    @if($task->assignee)
                        <div class="flex items-center gap-2">
                            <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                            <span>{{ $task->assignee->name }}</span>
                        </div>
                    @endif
                    @if($task->due_date)
                        <span class="{{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-erms-red' : '' }}">
                            กำหนดส่ง: {{ $task->due_date->translatedFormat('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Comments --}}
            <div class="card" wire:poll.15s>
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-base">ความคิดเห็น ({{ $task->comments->count() }})</h2>
                </div>
                <div class="divide-y divide-erms-border/50">
                    @foreach($task->comments as $comment)
                        <div class="px-5 py-3">
                            <div class="flex items-center gap-2 mb-1">
                                <img src="{{ $comment->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                <span class="text-sm font-medium">{{ $comment->user->name }}</span>
                                <span class="text-xs text-erms-muted">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-erms-text pl-8">{{ $comment->body }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="p-5 border-t border-erms-border">
                    <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="body" class="input-field flex-1" placeholder="เพิ่มความคิดเห็น..." required>
                        <button type="submit" class="btn-primary">ส่ง</button>
                    </form>
                </div>
            </div>

            {{-- Attachments --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-base">ไฟล์แนบ ({{ $task->attachments->count() }})</h2>
                </div>
                <div class="p-5">
                    @if($task->attachments->count())
                        <div class="space-y-2 mb-4">
                            @foreach($task->attachments as $attachment)
                                <div class="flex items-center justify-between bg-erms-surface-2 rounded-lg px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-erms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <div>
                                            <p class="text-sm">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-erms-muted">{{ number_format($attachment->file_size / 1024, 1) }} กิโลไบต์ • {{ $attachment->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('attachments.download', $attachment) }}" class="text-erms-blue hover:underline text-xs">ดาวน์โหลด</a>
                                        <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm('ลบไฟล์นี้?')">
                                            @csrf @method('DELETE')
                                            <button class="text-erms-red hover:underline text-xs">ลบ</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="flex gap-3">
                        @csrf
                        <input type="file" name="file" class="input-field flex-1 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-erms-blue/10 file:text-erms-blue file:cursor-pointer" required>
                        <button type="submit" class="btn-secondary text-xs">อัปโหลด</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Work Logs --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-sm">บันทึกเวลา ({{ number_format($task->workLogs->sum('hours'), 1) }} ชม.)</h2>
                </div>
                <div class="divide-y divide-erms-border/50 max-h-64 overflow-y-auto">
                    @forelse($task->workLogs as $log)
                        <div class="px-5 py-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-erms-muted">{{ $log->date->translatedFormat('d M') }} • {{ $log->user->name }}</span>
                                <span class="text-xs font-medium text-erms-blue">{{ number_format($log->hours, 1) }} ชม.</span>
                            </div>
                            @if($log->description)
                                <p class="text-xs text-erms-muted mt-0.5">{{ $log->description }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-4 text-center text-erms-muted text-xs">ยังไม่มีบันทึก</div>
                    @endforelse
                </div>
            </div>

            {{-- Task History --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-sm">ประวัติการเปลี่ยนแปลง</h2>
                </div>
                <div class="divide-y divide-erms-border/50 max-h-64 overflow-y-auto">
                    @forelse($task->taskUpdates->sortByDesc('created_at') as $update)
                        <div class="px-5 py-2.5">
                            <p class="text-xs">
                                <span class="font-medium">{{ $update->user->name }}</span>
                                @if($update->old_status)
                                    @php $sLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น']; @endphp
                                    <span class="badge-{{ str_replace('_', '-', $update->old_status) }} mx-0.5">{{ $sLabels[$update->old_status] ?? $update->old_status }}</span>
                                    <svg class="inline w-3 h-3 text-erms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                @endif
                                <span class="badge-{{ str_replace('_', '-', $update->new_status) }} mx-0.5">{{ $sLabels[$update->new_status] ?? $update->new_status }}</span>
                            </p>
                            @if($update->note)
                                <p class="text-xs text-erms-muted mt-0.5">{{ $update->note }}</p>
                            @endif
                            <p class="text-xs text-erms-muted mt-0.5">{{ $update->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-4 text-center text-erms-muted text-xs">ไม่มีประวัติ</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
