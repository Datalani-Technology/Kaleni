<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\MenuItem;
use App\Models\SpecialRequest;

/**
 * Single source of truth for the admin bell's notification data, shared by
 * the always-rendered admin.layout composer and the JS polling endpoint
 * (Admin\NotificationController) so the two never drift apart.
 */
class AdminNotifications
{
    public static function compute(): array
    {
        $threshold = (int) config('inventory.low_stock_threshold', 5);

        $pendingBookingsQuery = Booking::where('booking_status', 'pending')->where('order_type', Booking::ORDER_TYPE_CATERING_BOOKING);
        $pendingOrdersQuery = Booking::where('booking_status', 'pending')->where('order_type', Booking::ORDER_TYPE_QUICK_ORDER);
        $lowStockQuery = MenuItem::where('is_active', true)->where('stock', '<=', $threshold);
        $unreadContactsQuery = Contact::where('is_read', false);
        $newSpecialRequestsQuery = SpecialRequest::where('status', 'new')->where('source', SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE);
        $newQuoteRequestsQuery = SpecialRequest::where('status', 'new')->where('source', SpecialRequest::SOURCE_HOME_QUOTE_FORM);

        $pendingBookingCount = (clone $pendingBookingsQuery)->count();
        $pendingOrderCount = (clone $pendingOrdersQuery)->count();
        $lowStockCount = (clone $lowStockQuery)->count();
        $unreadContactCount = (clone $unreadContactsQuery)->count();
        $newSpecialRequestCount = (clone $newSpecialRequestsQuery)->count();
        $newQuoteRequestCount = (clone $newQuoteRequestsQuery)->count();

        return [
            'total' => $pendingBookingCount + $pendingOrderCount + $lowStockCount + $unreadContactCount + $newSpecialRequestCount + $newQuoteRequestCount,
            'pending_order_count' => $pendingBookingCount,
            'pending_quick_order_count' => $pendingOrderCount,
            'low_stock_count' => $lowStockCount,
            'unread_contact_count' => $unreadContactCount,
            'new_special_request_count' => $newSpecialRequestCount,
            'new_quote_request_count' => $newQuoteRequestCount,
            'orders' => $pendingBookingsQuery->latest()->limit(5)->get(['id', 'booking_number', 'customer_name', 'total_amount', 'created_at']),
            'quick_orders' => $pendingOrdersQuery->latest()->limit(5)->get(['id', 'booking_number', 'customer_name', 'total_amount', 'created_at']),
            'products' => $lowStockQuery->orderBy('stock')->limit(5)->get(['id', 'name', 'stock', 'updated_at']),
            'contacts' => $unreadContactsQuery->latest()->limit(5)->get(['id', 'name', 'subject', 'created_at']),
            'special_requests' => $newSpecialRequestsQuery->latest()->limit(5)->get(['id', 'name', 'occasion', 'event_date', 'created_at']),
            'quote_requests' => $newQuoteRequestsQuery->latest()->limit(5)->get(['id', 'name', 'occasion', 'event_date', 'created_at']),
        ];
    }
}
