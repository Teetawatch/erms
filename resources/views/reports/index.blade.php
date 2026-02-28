<x-app-layout>
    <x-slot name="header">รายงาน</x-slot>

    {{-- Filter --}}
    <div class="card p-5 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm text-erms-muted mb-1">พนักงาน</label>
                <select name="user_id" class="input-field w-48">
                    <option value="">เลือกพนักงาน</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected($selectedUserId == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">เดือน</label>
                <input type="month" name="month" value="{{ $month }}" class="input-field w-44">
            </div>
            <button type="submit" class="btn-primary">ดูรายงาน</button>
            @if($selectedUserId && $tasks->count())
                <a href="{{ route('reports.export-pdf', ['user_id' => $selectedUserId, 'month' => $month]) }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    ส่งออก PDF
                </a>
            @endif
        </form>
    </div>

    @if($selectedUserId && $tasks->count())
        {{-- Summary --}}
        <div class="card p-5 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">สรุปงานที่มอบหมาย</h3>
                <span class="text-2xl font-heading font-bold text-erms-green">{{ $completedTasks }}/{{ $tasks->count() }} งาน</span>
            </div>
        </div>

        {{-- Task Table --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-erms-border bg-erms-surface-2">
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">วันที่สร้าง</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">โครงการ</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">สถานะ</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ความสำคัญ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erms-border/50">
                    @foreach($tasks as $task)
                        <tr class="hover:bg-erms-surface-2 transition">
                            <td class="px-5 py-3 text-erms-muted whitespace-nowrap">{{ $task->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-3">{{ $task->title }}</td>
                            <td class="px-5 py-3 text-erms-muted">{{ $task->project->name ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 text-xs rounded-full @if($task->status == 'done') bg-green-100 text-green-800 @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800 @elseif($task->status == 'review') bg-yellow-100 text-yellow-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ $task->status == 'done' ? 'เสร็จ' : ($task->status == 'in_progress' ? 'กำลังทำ' : ($task->status == 'review' ? 'รอตรวจ' : 'ยังไม่ได้ทำ')) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 text-xs rounded-full @if($task->priority == 'urgent') bg-red-100 text-red-800 @elseif($task->priority == 'high') bg-orange-100 text-orange-800 @elseif($task->priority == 'medium') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ $task->priority == 'urgent' ? 'เร่งด่วน' : ($task->priority == 'high' ? 'สูง' : ($task->priority == 'medium' ? 'ปานกลาง' : 'ต่ำ')) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($selectedUserId)
        <div class="card p-8 text-center text-erms-muted">ไม่พบข้อมูลงานในเดือนที่เลือก</div>
    @else
        <div class="card p-8 text-center text-erms-muted">กรุณาเลือกพนักงานเพื่อดูรายงาน</div>
    @endif
</x-app-layout>
