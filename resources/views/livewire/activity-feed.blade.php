<div wire:poll.15s class="card">
    <div class="px-5 py-4 border-b border-erms-border">
        <h2 class="font-heading font-bold text-base">กิจกรรมล่าสุด</h2>
    </div>
    <div class="divide-y divide-erms-border/50 max-h-[500px] overflow-y-auto">
        @forelse($this->activities as $activity)
            <div class="flex items-start gap-3 px-5 py-3">
                <img src="{{ $activity->user->avatar_url ?? '' }}" alt="" class="w-7 h-7 rounded-full mt-0.5 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm">
                        <span class="font-medium text-erms-text">{{ $activity->user->name ?? 'ระบบ' }}</span>
                        <span class="text-erms-muted">
                            อัปเดต
                            <span class="text-erms-text font-medium">{{ $activity->task->title ?? '-' }}</span>
                        </span>
                        @if($activity->old_status && $activity->new_status)
                            @php $sLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น']; @endphp
                            <span class="badge-{{ str_replace('_', '-', $activity->old_status) }} mx-0.5">{{ $sLabels[$activity->old_status] ?? $activity->old_status }}</span>
                            <svg class="inline w-3 h-3 text-erms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="badge-{{ str_replace('_', '-', $activity->new_status) }} mx-0.5">{{ $sLabels[$activity->new_status] ?? $activity->new_status }}</span>
                        @elseif($activity->new_status)
                            @php $sLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น']; @endphp
                            <span class="badge-{{ str_replace('_', '-', $activity->new_status) }} mx-0.5">{{ $sLabels[$activity->new_status] ?? $activity->new_status }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-erms-muted mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-erms-muted text-sm">ยังไม่มีกิจกรรม</div>
        @endforelse
    </div>
</div>
