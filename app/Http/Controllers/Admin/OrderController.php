<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Quick food orders (order_type = quick_order): placed for pickup/delivery
 * without booking a full catered event. Event bookings live on their own
 * board — see Admin\BookingController — even though both share this same
 * Booking model and the same detail/update/invoice workflow there (a quick
 * order's "View" action still opens admin.bookings.show).
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->filteredQuery($request)->get();

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Customer', 'Email', 'Phone', 'Fulfillment', 'Total (N$)', 'Payment Method', 'Payment Status', 'Order Status', 'Placed At']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->booking_number,
                    $o->customer_name,
                    $o->customer_email,
                    $o->customer_phone,
                    ucfirst($o->fulfillment_method ?? 'N/A'),
                    number_format((float) $o->total_amount, 2, '.', ''),
                    $o->payment_method,
                    $o->payment_status,
                    $o->booking_status,
                    $o->created_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, 'orders-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function filteredQuery(Request $request)
    {
        $query = Booking::with('items.menuItem')->where('order_type', Booking::ORDER_TYPE_QUICK_ORDER)->latest();

        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('booking_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        return $query;
    }
}
