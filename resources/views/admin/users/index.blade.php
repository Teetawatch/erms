<x-app-layout>
    <x-slot name="header">จัดการผู้ใช้งาน</x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-erms-green/15 border border-erms-green/20 rounded-lg text-sm text-erms-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-erms-red/15 border border-erms-red/20 rounded-lg text-sm text-erms-red">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-erms-muted">ผู้ใช้ทั้งหมด {{ $users->total() }} คน</p>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เพิ่มผู้ใช้
        </a>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-erms-border bg-erms-surface-2">
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">ชื่อ</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">อีเมล</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">แผนก</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">บทบาท</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-erms-border/50">
                @foreach($users as $user)
                    <tr class="hover:bg-erms-surface-2 transition">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                <span class="font-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-erms-muted">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-erms-muted">{{ $user->department->name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @foreach($user->roles as $role)
                                @php $roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'manager' => 'ผู้จัดการ', 'employee' => 'พนักงาน']; @endphp
                                <span class="badge-{{ $role->name === 'admin' ? 'urgent' : ($role->name === 'manager' ? 'high' : 'low') }}">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-erms-blue hover:underline text-xs">แก้ไข</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('ต้องการลบผู้ใช้นี้?')">
                                        @csrf @method('DELETE')
                                        <button class="text-erms-red hover:underline text-xs">ลบ</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
</x-app-layout>
