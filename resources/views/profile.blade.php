<x-app-layout>
    <x-slot name="header">โปรไฟล์</x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            <livewire:profile.update-profile-information-form />
        </div>

        <div class="card p-6">
            <livewire:profile.update-password-form />
        </div>

        <div class="card p-6">
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-app-layout>
