<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            fputcsv($out, ['Order #', 'Customer', 'Email', 'Phone', 'Total (N$)', 'Payment Method', 'Payment Status', 'Order Status', 'Placed At']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->order_number,
                    $o->customer_name,
                    $o->customer_email,
                    $o->customer_phone,
                    number_format((float) $o->total_amount, 2, '.', ''),
                    $o->payment_method,
                    $o->payment_status,
                    $o->order_status,
                    $o->created_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, 'orders-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.invoice', compact('order'));
    }

    private function filteredQuery(Request $request)
    {
        $query = Order::with('items.product')->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        return $query;
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:pending,completed,failed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $updates = array_filter([
            'order_status' => $validated['order_status'] ?? null,
            'payment_status' => $validated['payment_status'] ?? null,
            'admin_notes' => $request->has('admin_notes') ? ($validated['admin_notes'] ?? '') : null,
        ], fn ($v) => $v !== null);

        if (!empty($updates)) {
            $order->update($updates);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated.');
    }
}
