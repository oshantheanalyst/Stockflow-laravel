@extends('layouts.app')
@section('title', 'Sales & Invoices')
@section('content')
<div class="card mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-text-primary">Sales & Invoices</h2><p class="text-sm text-text-secondary">Manage customer invoices and sales</p></div>
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" class="flex items-center gap-2"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice or customer..." class="form-input w-56" /><button type="submit" class="btn-primary">Search</button></form>
            <a href="{{ route('sales.index') }}" class="btn-ghost">↻ Refresh</a>
            <a href="{{ route('sales.create') }}" class="btn-primary">+ New Sale</a>
        </div>
    </div>
</div>
<div class="card overflow-x-auto">
    <table class="data-table">
        <thead><tr><th>INV NO</th><th>DATE</th><th>CUSTOMER</th><th>PAYMENT</th><th>TOTAL</th><th>STATUS</th>@if(auth()->user()->canDelete())<th>ACTIONS</th>@endif</tr></thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td class="font-mono text-xs font-medium">{{ $inv->invoice_no }}</td>
                <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                <td>{{ $inv->customer->name ?? 'Walk-in' }}</td>
                <td>{{ $inv->payment_method }}</td>
                <td class="font-semibold">Rs {{ number_format($inv->total, 2) }}</td>
                <td><span class="{{ $inv->is_paid ? 'badge-success' : 'badge-warning' }}">{{ $inv->status }}</span></td>
                @if(auth()->user()->canDelete())
                <td>
                    <form method="POST" action="{{ route('sales.destroy', $inv->id) }}" onsubmit="return confirm('Delete invoice {{ $inv->invoice_no }}?')">@csrf @method('DELETE')<button class="text-danger hover:underline text-xs">🗑 Delete</button></form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-text-secondary py-8">No invoices found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
