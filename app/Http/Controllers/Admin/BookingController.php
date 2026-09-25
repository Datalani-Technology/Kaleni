<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function export(Request $request): StreamedResponse
    {
        $bookings = $this->filteredQuery($request)->get();

        return response()->streamDownload(function () use ($bookings) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Booking #', 'Customer', 'Email', 'Phone', 'Event Date', 'Guests', 'Total (N$)', 'Payment Method', 'Payment Status', 'Booking Status', 'Placed At']);
            foreach ($bookings as $b) {
                fputcsv($out, [
                    $b->booking_number,
                    $b->customer_name,
                    $b->customer_email,
                    $b->customer_phone,
                    optional($b->event_date)->format('Y-m-d'),
                    $b->guest_count,
                    number_format((float) $b->total_amount, 2, '.', ''),
                    $b->payment_method,
                    $b->payment_status,
                    $b->booking_status,
                    $b->created_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, 'bookings-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function show(Booking $booking)
    {
        $booking->load('items.menuItem');
        return view('admin.bookings.show', compact('booking'));
    }

    public function invoice(Booking $booking)
    {
        $booking->load('items.menuItem');
        return view('admin.bookings.invoice', compact('booking'));
    }

    /**
     * Re-sends the confirmation email (which already carries the invoice/
     * receipt link and full breakdown) on demand, so admin can hand the
     * customer their invoice by email at any point after checkout.
     */
    public function emailInvoice(Booking $booking)
    {
        try {
            Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Invoice email failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Could not send the invoice email. Please try again or contact the customer directly.');
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Invoice emailed to ' . $booking->customer_email . '.');
    }

    private function filteredQuery(Request $request)
    {
        $query = Booking::with('items.menuItem')->where('order_type', Booking::ORDER_TYPE_CATERING_BOOKING)->latest();

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

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'booking_status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:pending,completed,failed',
            'total_amount' => 'nullable|numeric|min:0|max:999999.99',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $updates = array_filter([
            'booking_status' => $validated['booking_status'] ?? null,
            'payment_status' => $validated['payment_status'] ?? null,
            'total_amount' => $validated['total_amount'] ?? null,
            'admin_notes' => $request->has('admin_notes') ? ($validated['admin_notes'] ?? '') : null,
        ], fn ($v) => $v !== null);

        if (!empty($updates)) {
            $booking->update($updates);
        }

        $isQuickOrder = $booking->order_type === Booking::ORDER_TYPE_QUICK_ORDER;

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', $isQuickOrder ? 'Order updated.' : 'Booking updated.');
    }

    /**
     * Permanently removes a booking/order. Since stock was decremented when
     * it was placed, deleting it restores those units — this is meant for
     * clearing out test or mistaken entries, not for retiring a real,
     * fulfilled order (use the "Cancelled" status for that instead, which
     * keeps the record for revenue/reporting history).
     */
    public function destroy(Booking $booking)
    {
        $booking->load('items');
        $isQuickOrder = $booking->order_type === Booking::ORDER_TYPE_QUICK_ORDER;
        $number = $booking->booking_number;

        foreach ($booking->items as $item) {
            MenuItem::where('id', $item->menu_item_id)->increment('stock', $item->quantity);
        }

        $booking->delete();

        return redirect()->route($isQuickOrder ? 'admin.orders.index' : 'admin.bookings.index')
            ->with('success', ($isQuickOrder ? 'Order ' : 'Booking ') . $number . ' deleted and its stock restored.');
    }
}
