<?php

namespace App\Support;

use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(string $action, array $meta = []): void
    {
        $user = Auth::guard('web')->user();

        AdminAuditLog::create([
            'user_id' => $user?->id,
            'email' => $meta['email'] ?? $user?->email,
            'action' => $action,
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 512),
            'meta' => $meta,
        ]);
    }
}
