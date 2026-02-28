<x-app-layout>
    <x-slot name="header">{{ $task->title }}</x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-erms-green/15 border border-erms-green/20 rounded-lg text-sm text-erms-green">
            {{ session('success') }}
        </div>
    @endif

    <livewire:task-detail :task="$task" />
</x-app-layout>
