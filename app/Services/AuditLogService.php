<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Record an audit log entry for admin actions.
     */
    public static function log(string $action, ?string $targetType = null, ?int $targetId = null, array $details = []): AuditLog
    {
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'admin_name' => $user ? $user->name : 'System',
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
