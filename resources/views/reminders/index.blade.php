@extends('layouts.app')
@section('title', 'Reminders & Due Payments')
@section('content')
<div class="card mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-text-primary">Reminders & Due Payments</h2><p class="text-sm text-text-secondary">Track overdue invoices and supplier payments</p></div>
        <div><a href="{{ route('reminders.index') }}" class="btn-ghost">↻ Refresh</a></div>
    </div>
</div>
<div class="card overflow-x-auto">
    <table class="data-table">
        <thead><tr><th>TYPE</th><th>REFERENCE</th><th>CUSTOMER/SUPPLIER</th><th>AMOUNT</th><th>DUE DATE</th><th>DAYS OVER</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
        <tbody>
            @forelse($reminders as $r)
            <tr>
                <td><span class="font-semibold {{ $r['type'] === 'Invoice' ? 'text-blue' : 'text-purple-600' }}">{{ $r['type'] }}</span></td>
                <td class="font-mono text-xs">{{ $r['reference'] }}</td>
                <td class="font-medium">{{ $r['name'] }}</td>
                <td class="font-semibold">Rs {{ number_format($r['amount'], 2) }}</td>
                <td>{{ $r['due_date'] ? $r['due_date']->format('d M Y') : '-' }}</td>
                <td class="{{ $r['days_overdue'] > 0 ? 'text-danger font-bold' : '' }}">{{ $r['days_overdue'] }}</td>
                <td><span class="{{ $r['status'] === 'Overdue' ? 'badge-danger' : 'badge-warning' }}">{{ $r['status'] }}</span></td>
                <td>
                    <button onclick="markComplete({{ $r['id'] }}, '{{ $r['type'] }}')" class="btn-success px-3 py-1 text-xs">✔ Complete</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-text-secondary py-8">No pending reminders. You are all caught up!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script>
function markComplete(id, type) {
    if(!confirm('Mark this ' + type.toLowerCase() + ' as completed/paid?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    fetch('/reminders/' + id + '/complete', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ type: type })
    }).then(r => r.json()).then(() => window.location.reload());
}
</script>
@endpush
