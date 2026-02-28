<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function __invoke()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(25);

        return view('admin.audit-log', compact('logs'));
    }
}
