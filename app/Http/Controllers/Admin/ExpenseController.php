<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $expenses = (clone $query)->paginate(20)->withQueryString();
        $totalFiltered = $query->sum('amount');

        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'totalFiltered' => $totalFiltered,
            'categories' => Expense::CATEGORIES,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $expenses = $this->filteredQuery($request)->get();

        return response()->streamDownload(function () use ($expenses) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Category', 'Description', 'Amount (N$)', 'Logged by']);
            foreach ($expenses as $e) {
                fputcsv($out, [
                    $e->spent_at->format('Y-m-d'),
                    $e->category,
                    $e->description,
                    number_format((float) $e->amount, 2, '.', ''),
                    $e->user->name ?? '',
                ]);
            }
            fclose($out);
        }, 'expenses-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function filteredQuery(Request $request)
    {
        $query = Expense::with('user')->orderByDesc('spent_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('spent_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('spent_at', '<=', $request->date('to'));
        }

        return $query;
    }

    public function create()
    {
        return view('admin.expenses.create', ['categories' => Expense::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'spent_at' => 'required|date',
        ]);

        Expense::create($validated + ['user_id' => auth()->id()]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense logged.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }
}
