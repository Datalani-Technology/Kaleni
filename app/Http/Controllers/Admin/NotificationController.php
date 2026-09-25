<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNotifications;

class NotificationController extends Controller
{
    /**
     * Polled by the admin layout's JS every so often so a new booking,
     * quote request, enquiry, etc. shows up on the bell without the admin
     * having to manually refresh an already-open page.
     */
    public function poll()
    {
        $adminNotifications = AdminNotifications::compute();

        return response()->json([
            'total' => $adminNotifications['total'],
            'pending_order_count' => $adminNotifications['pending_order_count'],
            'pending_quick_order_count' => $adminNotifications['pending_quick_order_count'],
            'new_special_request_count' => $adminNotifications['new_special_request_count'],
            'new_quote_request_count' => $adminNotifications['new_quote_request_count'],
            'html' => view('admin.partials.notification-centre', compact('adminNotifications'))->render(),
        ]);
    }
}
