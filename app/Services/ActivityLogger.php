<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $module, ?int $recordId = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'timestamp' => now(),
        ]);
    }
}
