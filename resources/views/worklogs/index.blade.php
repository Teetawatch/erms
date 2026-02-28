<x-app-layout>
    <x-slot name="header">บันทึกเวลาทำงาน</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <livewire:work-log-form />
        </div>
        <div>
            <livewire:timer />
        </div>
    </div>
</x-app-layout>
