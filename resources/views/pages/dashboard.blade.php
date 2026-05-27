@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

{{-- Welcome Message Card --}}
<div class="card mb-6 bg-gradient-to-r from-indigo-500/10 to-violet-500/10 border-indigo-500/20">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-indigo-950 dark:text-indigo-200">Welcome to your StockFlow Workspace</h2>
            <p class="text-xs text-indigo-800/70 dark:text-indigo-300/80 mt-1">Real-time business telemetry, sales invoicing, and inventory level monitoring</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/sales/create') }}" class="btn-primary flex items-center gap-1.5 cursor-pointer">
                <span>+ New Sale Invoice</span>
            </a>
        </div>
    </div>
</div>

{{-- Dynamic Dashboard stats grid --}}
<livewire:dashboard-stats />

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Quick links or other interactive panels --}}
        <div class="card bg-white">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">System Activity Telemetry</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ url('/products') }}" class="p-4 bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-100 rounded-2xl text-center group transition-all duration-200">
                    <span class="text-indigo-500 group-hover:scale-110 inline-block transition-transform duration-200 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </span>
                    <h4 class="text-xs font-bold text-slate-800">Inventory</h4>
                </a>
                <a href="{{ url('/customers') }}" class="p-4 bg-slate-50 hover:bg-emerald-50/50 border border-slate-100 hover:border-emerald-100 rounded-2xl text-center group transition-all duration-200">
                    <span class="text-emerald-500 group-hover:scale-110 inline-block transition-transform duration-200 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <h4 class="text-xs font-bold text-slate-800">Customers</h4>
                </a>
                <a href="{{ url('/sales') }}" class="p-4 bg-slate-50 hover:bg-amber-50/50 border border-slate-100 hover:border-amber-100 rounded-2xl text-center group transition-all duration-200">
                    <span class="text-amber-500 group-hover:scale-110 inline-block transition-transform duration-200 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <h4 class="text-xs font-bold text-slate-800">Sales History</h4>
                </a>
                <a href="{{ url('/reminders') }}" class="p-4 bg-slate-50 hover:bg-sky-50/50 border border-slate-100 hover:border-sky-100 rounded-2xl text-center group transition-all duration-200">
                    <span class="text-sky-500 group-hover:scale-110 inline-block transition-transform duration-200 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    <h4 class="text-xs font-bold text-slate-800">Reminders</h4>
                </a>
            </div>
        </div>
        
        <div class="card bg-white">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Live Workspace Information</h3>
            <p class="text-xs text-slate-500 leading-relaxed">You are currently logged into the StockFlow business telemetry workspace using **Laravel Jetstream session authentication**. All administrative user and inventory records utilize proper **MySQL foreign keys & Eloquent relationships**.</p>
        </div>
    </div>
    
    <div>
        {{-- Live stock level monitoring component --}}
        <livewire:stock-monitor />
    </div>
</div>

@endsection
