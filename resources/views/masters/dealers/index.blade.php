@extends('layouts.app')
@section('title', 'Dealer Master')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-950 tracking-tight">Dealer Master</h1>
            <p class="text-slate-500 font-medium">Manage relationships with feed, chick, and medicine suppliers</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="document.getElementById('add-dealer-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95">
                + Register Dealer
            </button>
        </div>
    </div>

    {{-- Dealer Insights --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-emerald-200 transition-all">
            <div
                class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <span class="material-symbols-rounded">groups</span>
            </div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Dealers</h3>
                <p class="text-2xl font-black text-slate-950">{{ $dealers->total() }}</p>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-red-200 transition-all">
            <div
                class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <span class="material-symbols-rounded">payments</span>
            </div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Payable</h3>
                <p class="text-2xl font-black text-slate-950">Rs {{ number_format($dealers->sum('pending_amount'), 0) }}</p>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-red-50 via-white to-red-50/30 border border-red-100 p-6 rounded-2xl shadow-md shadow-red-100/40 flex items-center gap-6 group hover:border-red-200 transition-all">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <span class="material-symbols-rounded">notifications_active</span>
            </div>
            <div>
                <h3 class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Active Accounts</h3>
                <p class="text-2xl font-black text-slate-950">{{ $dealers->where('pending_amount', '>', 0)->count() }} with Dues
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div
        class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-lg overflow-hidden mb-12">
        <div
            class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
            <form method="GET" class="relative w-full max-w-md">
                <span class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by firm or contact..."
                    class="w-full pl-12 pr-4 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr
                        class="bg-gradient-to-r from-emerald-50/80 to-sky-50/80 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                        <th class="px-8 py-5">Firm & Location</th>
                        <th class="px-8 py-5">Point of Contact</th>
                        <th class="px-8 py-5">Operational Area</th>
                        <th class="px-8 py-5 text-right">Pending Balance</th>
                        <th class="px-8 py-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dealers as $dealer)
                        <tr class="hover:bg-emerald-50/30 transition-all group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center font-black text-slate-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                        {{ substr($dealer->firm_name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('masters.dealers.show', $dealer) }}"
                                            class="font-black text-slate-950 tracking-tight hover:text-emerald-600 transition-colors">{{ $dealer->firm_name }}</a>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $dealer->location ?: 'NO LOCATION' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span
                                        class="font-bold text-slate-950 leading-tight">{{ $dealer->contact_person ?: '-' }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                        {{ $dealer->phone }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="text-xs font-black text-slate-500 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">
                                    {{ $dealer->route ?: 'GENERAL' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                @if($dealer->pending_amount > 0)
                                    <span class="text-lg font-black text-red-500">Rs
                                        {{ number_format($dealer->pending_amount, 0) }}</span>
                                @else
                                    <span class="text-sm font-bold text-slate-300">CLEARED</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('masters.dealers.ledger-pdf', $dealer) }}"
                                        class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-lg transition-all"
                                        title="Download Ledger PDF">
                                        <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                                    </a>
                                    <button
                                        onclick="openEditDealer({{ $dealer->id }},'{{ addslashes($dealer->firm_name) }}','{{ addslashes($dealer->contact_person) }}','{{ $dealer->phone }}','{{ $dealer->gst_number }}','{{ addslashes($dealer->location) }}','{{ $dealer->route }}')"
                                        class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-lg transition-all">
                                        <span class="material-symbols-rounded text-lg">edit_square</span>
                                    </button>
                                    <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST"
                                        onsubmit="return confirm('Archive dealer record?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all">
                                            <span class="material-symbols-rounded text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                        <span class="material-symbols-rounded text-4xl">folder_open</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-950">No Dealers Recorded</h3>
                                    <p class="text-slate-400 font-medium mt-1">Register your first supplier profile.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dealers->hasPages())
            <div class="p-8 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
                {{ $dealers->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Add Dealer Modal --}}
    <div id="add-dealer-modal"
        class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-950/60 backdrop-blur-xl px-4 py-6 sm:px-6 transition-all duration-500">
        <div class="mx-auto flex min-h-full w-full max-w-2xl items-center justify-center">
            <div
                class="w-full flex flex-col overflow-hidden rounded-[2.5rem] border border-white/40 bg-white/90 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] backdrop-blur-2xl transition-all">
                {{-- Modal Header --}}
                <div
                    class="relative shrink-0 border-b border-slate-100 bg-gradient-to-r from-emerald-50/50 to-sky-50/50 px-10 py-8">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-400/10 blur-[80px] rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-sky-400/10 blur-[80px] rounded-full"></div>

                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-600/20">
                                    <span class="material-symbols-rounded text-xl">factory</span>
                                </div>
                                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">
                                    Register Dealer</h2>
                            </div>
                            <p class="text-sm text-slate-500 font-medium ml-13">Add a new supplier to your procurement
                                network</p>
                        </div>
                        <button onclick="document.getElementById('add-dealer-modal').classList.add('hidden')"
                            class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all shadow-sm active:scale-90">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('masters.dealers.store') }}" method="POST"
                    class="p-10 space-y-8 bg-white/50 relative z-10">
                    @csrf
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                Firm Identity *
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition-colors">corporate_fare</span>
                                <input type="text" name="firm_name" required placeholder="e.g. Superior Feed Mills"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black placeholder:text-slate-300">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-sky-500"></span>
                                Contact Person
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-sky-600 transition-colors">person</span>
                                <input type="text" name="contact_person" placeholder="e.g. Sales Manager"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all font-black placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-indigo-500"></span>
                                Phone Number *
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">call</span>
                                <input type="text" name="phone" required placeholder="+91 00000 00000"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black placeholder:text-slate-300">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                GSTIN
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-600 transition-colors">receipt_long</span>
                                <input type="text" name="gst_number" placeholder="Optional"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all font-bold uppercase placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                Location
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rose-600 transition-colors">location_on</span>
                                <input type="text" name="location" placeholder="e.g. Industrial Estate"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold placeholder:text-slate-300">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-violet-500"></span>
                                Route
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-violet-600 transition-colors">route</span>
                                <input type="text" name="route" placeholder="Supply route..."
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 outline-none transition-all font-bold placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex flex-col-reverse gap-4 pt-6 sm:flex-row sm:justify-end">
                        <button type="button" onclick="document.getElementById('add-dealer-modal').classList.add('hidden')"
                            class="px-8 py-5 text-sm font-black uppercase tracking-widest text-slate-400 hover:text-slate-950 transition-colors rounded-[1.25rem] hover:bg-slate-50">
                            Discard
                        </button>
                        <button type="submit"
                            class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-[1.25rem] bg-slate-950 px-10 py-5 text-sm font-black text-white shadow-2xl transition-all hover:scale-[1.02] active:scale-95">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-sky-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                            <span class="relative z-10 flex items-center gap-2">
                                <span class="material-symbols-rounded">domain_add</span>
                                Register Dealer
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Edit Dealer Modal --}}
    <div id="edit-dealer-modal"
        class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-950/60 backdrop-blur-xl px-4 py-6 sm:px-6 transition-all duration-500">
        <div class="mx-auto flex min-h-full w-full max-w-2xl items-center justify-center">
            <div
                class="w-full flex flex-col overflow-hidden rounded-[2.5rem] border border-white/40 bg-white/90 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] backdrop-blur-2xl transition-all">
                {{-- Modal Header --}}
                <div
                    class="relative shrink-0 border-b border-slate-100 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 px-10 py-8">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-400/10 blur-[80px] rounded-full"></div>

                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                                    <span class="material-symbols-rounded text-xl">edit_square</span>
                                </div>
                                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Edit
                                    Supplier</h2>
                            </div>
                            <p class="text-sm text-slate-500 font-medium ml-13">Modify existing dealer credentials</p>
                        </div>
                        <button onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')"
                            class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all shadow-sm active:scale-90">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form id="edit-dealer-form" method="POST" class="p-10 space-y-8 bg-white/50 relative z-10">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                                Firm Name
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">corporate_fare</span>
                                <input type="text" name="firm_name" id="ed-firm" required
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-black">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-indigo-500"></span>
                                Contact Person
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">person</span>
                                <input type="text" name="contact_person" id="ed-contact"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                                Phone Number
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-slate-600 transition-colors">call</span>
                                <input type="text" name="phone" id="ed-phone" required
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-slate-500/10 focus:border-slate-500 outline-none transition-all font-black">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                GSTIN
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-600 transition-colors">receipt_long</span>
                                <input type="text" name="gst_number" id="ed-gst"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all font-bold uppercase">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                Location
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rose-600 transition-colors">location_on</span>
                                <input type="text" name="location" id="ed-location"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                <span class="w-1 h-1 rounded-full bg-violet-500"></span>
                                Route
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-rounded absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-violet-600 transition-colors">route</span>
                                <input type="text" name="route" id="ed-route"
                                    class="w-full pl-14 pr-6 py-5 bg-slate-50/50 border border-slate-200 rounded-[1.25rem] focus:ring-4 focus:ring-violet-500/10 focus:border-violet-500 outline-none transition-all font-bold">
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex flex-col-reverse gap-4 pt-6 sm:flex-row sm:justify-end">
                        <button type="button" onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')"
                            class="px-8 py-5 text-sm font-black uppercase tracking-widest text-slate-400 hover:text-slate-950 transition-colors rounded-[1.25rem] hover:bg-slate-50">
                            Cancel Changes
                        </button>
                        <button type="submit"
                            class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-[1.25rem] bg-slate-950 px-10 py-5 text-sm font-black text-white shadow-2xl transition-all hover:scale-[1.02] active:scale-95">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                            <span class="relative z-10 flex items-center gap-2">
                                <span class="material-symbols-rounded">save</span>
                                Commit Updates
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openEditDealer(id, firm, contact, phone, gst, location, route) {
            document.getElementById('edit-dealer-form').action = `/masters/dealers/${id}`;
            document.getElementById('ed-firm').value = firm;
            document.getElementById('ed-contact').value = contact;
            document.getElementById('ed-phone').value = phone;
            document.getElementById('ed-gst').value = gst;
            document.getElementById('ed-location').value = location;
            document.getElementById('ed-route').value = route;
            document.getElementById('edit-dealer-modal').classList.remove('hidden');
        }
    </script>
@endpush