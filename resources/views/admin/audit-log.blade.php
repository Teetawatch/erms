<x-app-layout>
    <x-slot name="header">บันทึกการใช้งาน</x-slot>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-erms-border bg-erms-surface-2">
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">เวลา</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">ผู้ใช้</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">เหตุการณ์</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">เป้าหมาย</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">รายละเอียด</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-erms-border/50">
                @foreach($logs as $log)
                    <tr class="hover:bg-erms-surface-2 transition">
                        <td class="px-5 py-3 text-erms-muted whitespace-nowrap text-xs">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td class="px-5 py-3">{{ $log->causer?->name ?? 'ระบบ' }}</td>
                        <td class="px-5 py-3">
                            <span class="badge-{{ $log->event === 'deleted' ? 'urgent' : ($log->event === 'updated' ? 'medium' : 'low') }}">
                                @php $eventLabels = ['created' => 'สร้าง', 'updated' => 'แก้ไข', 'deleted' => 'ลบ']; @endphp
                                {{ $eventLabels[$log->event] ?? $log->event ?? $log->description }}
                            </span>
                        </td>
                        @php $modelLabels = ['User' => 'ผู้ใช้', 'Project' => 'โครงการ', 'Task' => 'งาน', 'Department' => 'แผนก', 'WorkLog' => 'บันทึกเวลา', 'Comment' => 'ความคิดเห็น', 'Attachment' => 'ไฟล์แนบ']; @endphp
                        <td class="px-5 py-3 text-erms-muted">{{ $modelLabels[class_basename($log->subject_type ?? '')] ?? class_basename($log->subject_type ?? '') }} #{{ $log->subject_id }}</td>
                        <td class="px-5 py-3 text-xs text-erms-muted">
                            @if($log->properties && $log->properties->count())
                                <details>
                                    <summary class="cursor-pointer text-erms-blue hover:underline">ดูรายละเอียด</summary>
                                    <pre class="mt-1 text-xs bg-erms-surface-2 p-2 rounded overflow-x-auto max-w-xs">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
</x-app-layout>
