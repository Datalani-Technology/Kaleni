@extends('admin.layout')

@section('title', 'Log Expense')
@section('sidebar_active', 'expenses')

@section('content')
<h1 class="mb-4">Log Expense</h1>

<div class="card" style="max-width: 560px;">
    <div class="card-body">
        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="amount" class="form-label">Amount (N$)</label>
                <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required autofocus>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                    <option value="">Choose a category</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ old('category') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="spent_at" class="form-label">Date</label>
                <input type="date" class="form-control @error('spent_at') is-invalid @enderror" id="spent_at" name="spent_at" value="{{ old('spent_at', now()->toDateString()) }}" required>
                @error('spent_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="description" class="form-label">Description (optional)</label>
                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" maxlength="255" placeholder="e.g. Fresh roses from supplier">
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
