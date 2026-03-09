<x-app-layout>
    <x-slot name="header">จัดการฝ่าย</x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-erms-green/15 border border-erms-green/20 rounded-lg text-sm text-erms-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-erms-red/15 border border-erms-red/20 rounded-lg text-sm text-erms-red">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-erms-muted">ฝ่ายทั้งหมด {{ $departments->total() }} รายการ</p>
        <a href="{{ route('admin.departments.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus mr-1.5"></i>
            เพิ่มฝ่าย
        </a>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-erms-border bg-erms-surface-2">
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">ชื่อฝ่าย</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase">รายละเอียด</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-erms-muted uppercase">จำนวนพนักงาน</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-erms-border/50">
                @foreach($departments as $dept)
                    <tr class="hover:bg-erms-surface-2 transition">
                        <td class="px-5 py-3 font-medium">{{ $dept->name }}</td>
                        <td class="px-5 py-3 text-erms-muted">{{ $dept->description ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">{{ $dept->users_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.departments.edit', $dept) }}" class="text-erms-blue hover:underline text-xs">แก้ไข</a>
                                <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}" onsubmit="return confirm('ต้องการลบฝ่ายนี้?')">
                                    @csrf @method('DELETE')
                                    <button class="text-erms-red hover:underline text-xs">ลบ</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $departments->links() }}</div>
</x-app-layout>
