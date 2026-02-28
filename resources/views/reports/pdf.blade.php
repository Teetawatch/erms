<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>รายงานงานที่มอบหมาย - {{ $user->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        h2 { font-size: 14px; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f5f5f5; text-align: left; padding: 8px; border: 1px solid #ddd; font-size: 11px; }
        td { padding: 8px; border: 1px solid #ddd; }
        .status-done { background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .status-progress { background: #d1ecf1; color: #0c5460; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .status-review { background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .status-todo { background: #f8f9fa; color: #6c757d; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .header { border-bottom: 2px solid #4f8ef7; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานงานที่มอบหมาย</h1>
        <h2>{{ $user->name }} — {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h2>
        <p>งานที่เสร็จสิ้น: {{ $completedTasks }}/{{ $tasks->count() }} งาน</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>วันที่สร้าง</th>
                <th>งาน</th>
                <th>โครงการ</th>
                <th>สถานะ</th>
                <th>ความสำคัญ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->created_at->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->project->name ?? '-' }}</td>
                    <td>
                        @if($task->status == 'done')
                            <span class="status-done">เสร็จ</span>
                        @elseif($task->status == 'in_progress')
                            <span class="status-progress">กำลังทำ</span>
                        @elseif($task->status == 'review')
                            <span class="status-review">รอตรวจ</span>
                        @else
                            <span class="status-todo">ยังไม่ได้ทำ</span>
                        @endif
                    </td>
                    <td>{{ $task->priority == 'urgent' ? 'เร่งด่วน' : ($task->priority == 'high' ? 'สูง' : ($task->priority == 'medium' ? 'ปานกลาง' : 'ต่ำ')) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px; font-size: 10px; color: #999;">
        สร้างโดยระบบ ERMS เมื่อ {{ now()->translatedFormat('d/m/Y H:i') }}
    </p>
</body>
</html>
