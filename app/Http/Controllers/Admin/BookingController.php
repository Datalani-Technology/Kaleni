<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
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

    private function filteredQuery(Request $request)
    {
        $query = Booking::with('items.menuItem')->latest();

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
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $updates = array_filter([
            'booking_status' => $validated['booking_status'] ?? null,
            'payment_status' => $validated['payment_status'] ?? null,
            'admin_notes' => $request->has('admin_notes') ? ($validated['admin_notes'] ?? '') : null,
        ], fn ($v) => $v !== null);

        if (!empty($updates)) {
            $booking->update($updates);
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated.');
    }
}
