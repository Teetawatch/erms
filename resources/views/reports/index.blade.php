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
            @if($selectedUserId && $workLogs->count())
                <a href="{{ route('reports.export-pdf', ['user_id' => $selectedUserId, 'month' => $month]) }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    ส่งออก PDF
                </a>
            @endif
        </form>
    </div>

    @if($selectedUserId && $workLogs->count())
        {{-- Summary --}}
        <div class="card p-5 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">สรุปชั่วโมงทำงาน</h3>
                <span class="text-2xl font-heading font-bold text-erms-green">{{ number_format($totalHours, 1) }} ชม.</span>
            </div>
        </div>

        {{-- Work Log Table --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-erms-border bg-erms-surface-2">
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">วันที่</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">โครงการ</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ชั่วโมง</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">รายละเอียด</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erms-border/50">
                    @foreach($workLogs as $log)
                        <tr class="hover:bg-erms-surface-2 transition">
                            <td class="px-5 py-3 text-erms-muted whitespace-nowrap">{{ $log->date->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-3">{{ $log->task->title ?? '-' }}</td>
                            <td class="px-5 py-3 text-erms-muted">{{ $log->task->project->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-erms-blue">{{ number_format($log->hours, 2) }}</td>
                            <td class="px-5 py-3 text-erms-muted">{{ $log->description ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-erms-border bg-erms-surface-2">
                        <td colspan="3" class="px-5 py-3 font-medium text-right">รวม</td>
                        <td class="px-5 py-3 text-right font-bold text-erms-green">{{ number_format($totalHours, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @elseif($selectedUserId)
        <div class="card p-8 text-center text-erms-muted">ไม่พบข้อมูลบันทึกเวลาในเดือนที่เลือก</div>
    @else
        <div class="card p-8 text-center text-erms-muted">กรุณาเลือกพนักงานเพื่อดูรายงาน</div>
    @endif
</x-app-layout>
