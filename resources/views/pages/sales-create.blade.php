@extends('layouts.app')
@section('title', 'Create Sale')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-slate-200/40">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Create Sale & Invoice</h2>
        <p class="text-sm text-slate-400 mt-1">Register new sales transactions, calculate totals, and record receipts</p>
    </div>
    <a href="/sales" class="btn-ghost flex items-center gap-1.5 py-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Back to Sales</span>
    </a>
</div>

<livewire:order-manager />

@endsection
