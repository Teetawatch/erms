<div class="card p-6" x-data="timerWidget(@entangle('isRunning'), @entangle('startTime'))">
    <h2 class="font-heading font-bold text-base mb-4">จับเวลา</h2>

    {{-- Timer Display --}}
    <div class="flex flex-col items-center mb-6">
        <div class="w-36 h-36 rounded-full border-4 border-erms-border flex items-center justify-center mb-4 relative">
            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 144 144">
                <circle cx="72" cy="72" r="68" fill="none" stroke="#e2e6ef" stroke-width="4"/>
                <circle cx="72" cy="72" r="68" fill="none"
                    :stroke="isRunning ? '#16b97a' : '#4f8ef7'"
                    stroke-width="4" stroke-linecap="round"
                    :stroke-dasharray="2 * Math.PI * 68"
                    :stroke-dashoffset="2 * Math.PI * 68 * (1 - (elapsedSeconds % 3600) / 3600)"
                    class="transition-all duration-1000"/>
            </svg>
            <span class="text-2xl font-heading font-bold tabular-nums" x-text="formattedTime">00:00:00</span>
        </div>
    </div>

    {{-- Task Selection --}}
    <div class="space-y-3 mb-4">
        <select wire:model="selectedTaskId" class="input-field" :disabled="isRunning">
            <option value="">เลือกงาน</option>
            @foreach($this->tasks as $task)
                <option value="{{ $task->id }}">{{ $task->title }} ({{ $task->project->name ?? '-' }})</option>
            @endforeach
        </select>
        <input type="text" wire:model="description" class="input-field" placeholder="รายละเอียด (ไม่บังคับ)" :disabled="!isRunning">
    </div>

    {{-- Start/Stop Button --}}
    <div class="flex justify-center">
        <template x-if="!isRunning">
            <button wire:click="startTimer" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-erms-green hover:bg-erms-green/80 transition flex items-center gap-2"
                :disabled="!$wire.selectedTaskId">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                เริ่มจับเวลา
            </button>
        </template>
        <template x-if="isRunning">
            <button wire:click="stopTimer" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-erms-red hover:bg-erms-red/80 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12"/></svg>
                หยุดและบันทึก
            </button>
        </template>
    </div>
</div>

@script
<script>
    Alpine.data('timerWidget', (isRunningEntangle, startTimeEntangle) => ({
        isRunning: isRunningEntangle,
        startTime: startTimeEntangle,
        elapsedSeconds: 0,
        interval: null,
        formattedTime: '00:00:00',

        init() {
            this.$watch('isRunning', (val) => {
                if (val) {
                    this.startInterval();
                } else {
                    this.stopInterval();
                }
            });
            if (this.isRunning) {
                this.startInterval();
            }
        },

        startInterval() {
            this.interval = setInterval(() => {
                if (this.startTime) {
                    this.elapsedSeconds = Math.floor(Date.now() / 1000) - this.startTime;
                    this.formattedTime = this.formatTime(this.elapsedSeconds);
                }
            }, 1000);
        },

        stopInterval() {
            clearInterval(this.interval);
            this.elapsedSeconds = 0;
            this.formattedTime = '00:00:00';
        },

        formatTime(totalSeconds) {
            const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
            const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
            const s = (totalSeconds % 60).toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        }
    }));
</script>
@endscript
