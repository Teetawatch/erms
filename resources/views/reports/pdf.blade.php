<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>รายงานบันทึกเวลา - {{ $user->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        h2 { font-size: 14px; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f5f5f5; text-align: left; padding: 8px; border: 1px solid #ddd; font-size: 11px; }
        td { padding: 8px; border: 1px solid #ddd; }
        .text-right { text-align: right; }
        .total-row { background: #f9f9f9; font-weight: bold; }
        .header { border-bottom: 2px solid #4f8ef7; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานบันทึกเวลาทำงาน</h1>
        <h2>{{ $user->name }} — {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>วันที่</th>
                <th>งาน</th>
                <th>โครงการ</th>
                <th class="text-right">ชั่วโมง</th>
                <th>รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workLogs as $log)
                <tr>
                    <td>{{ $log->date->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $log->task->title ?? '-' }}</td>
                    <td>{{ $log->task->project->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($log->hours, 2) }}</td>
                    <td>{{ $log->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">รวมทั้งหมด</td>
                <td class="text-right">{{ number_format($totalHours, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 30px; font-size: 10px; color: #999;">
        สร้างโดยระบบ ERMS เมื่อ {{ now()->translatedFormat('d/m/Y H:i') }}
    </p>
</body>
</html>
