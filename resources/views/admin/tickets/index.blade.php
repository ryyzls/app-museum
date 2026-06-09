@extends('layouts.admin')

@section('title', 'Manage Tickets')
@section('page-title', 'Tickets')
@section('breadcrumb', 'Tickets')

@section('content')

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-14">
            <div>
                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                    Ticket Management
                </p>
                <h1 class="museum-title text-6xl font-light">
                    Tickets
                </h1>
            </div>
            <a href="{{ route('admin.tickets.create') }}"
                class="px-8 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">
                Create Ticket
            </a>
        </div>

        {{-- Filter Form --}}
        <form method="GET" class="flex items-center gap-4 mb-8">
            {{-- Search --}}
            <input type="text" name="search" placeholder="Search exhibition..." value="{{ request('search') }}"
                class="px-6 py-4 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/10 w-[320px]">

            {{-- Status --}}
            <select name="status" class="px-6 py-4 rounded-2xl border border-gray-200 focus:outline-none">

                <option value="">All Status</option>

                <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>
                    Available
                </option>

                <option value="Sold Out" {{ request('status') == 'Sold Out' ? 'selected' : '' }}>
                    Sold Out
                </option>

                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>
                    Closed
                </option>

            </select>

            <select name="type" class="px-6 py-4 rounded-2xl border border-gray-200 focus:outline-none">

                <option value="">All Types</option>

                <option value="vip" {{ request('type') == 'vip' ? 'selected' : '' }}>
                    VIP
                </option>

                <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>
                    Regular
                </option>

                <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>
                    Student
                </option>

            </select>

            {{-- Submit --}}
            <button type="submit"
                class="px-6 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">
                Filter
            </button>
        </form>

        {{-- Table Card --}}
        <div
            class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

            {{-- Wrapper khusus agar scrollbar muncul di bawah tabel --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[1200px]">
                    <thead class="border-b border-gray-100">
                        <tr class="text-left">
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Exhibition</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Ticket Type</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Visit Date</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Price</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Quota</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Remaining</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Status</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr class="border-b border-gray-100 hover:bg-black/5 transition duration-200">

                                {{-- Exhibition --}}
                                <td class="px-8 py-6 align-middle">
                                    <div class="flex items-center gap-5">
                                        <img src="{{ str_starts_with($ticket->exhibition->banner_image ?? '', 'http') ? $ticket->exhibition->banner_image : asset('storage/' . $ticket->exhibition->banner_image) }}"
                                            alt="{{ $ticket->exhibition->title ?? 'Banner' }}"
                                            class="w-28 h-20 rounded-2xl object-cover flex-shrink-0">
                                        <div class="min-w-[220px]">
                                            <p class="font-medium leading-relaxed">
                                                {{ $ticket->exhibition->title ?? '-' }}
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $ticket->exhibition->subtitle ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Ticket Type --}}
                                <td class="px-8 py-6 align-middle whitespace-nowrap capitalize">
                                    {{ $ticket->ticket_type }}
                                </td>

                                {{-- Visit Date --}}
                                <td class="px-8 py-6 align-middle whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($ticket->visit_date)->format('d M Y') }}
                                </td>

                                {{-- Price --}}
                                <td class="px-8 py-6 align-middle whitespace-nowrap">
                                    €{{ number_format($ticket->price, 0) }}
                                </td>

                                {{-- Quota --}}
                                <td class="px-8 py-6 align-middle whitespace-nowrap">
                                    {{ $ticket->quota }}
                                </td>

                                {{-- Remaining --}}
                                <td class="px-8 py-6 align-middle whitespace-nowrap">
                                    {{ $ticket->available_quota }}
                                </td>

                                {{-- Status --}}
                                <td class="px-8 py-6 align-middle">

                                    @if ($ticket->status === 'Available')

                                        <x-admin.status-badge type="success">
                                            Available
                                        </x-admin.status-badge>

                                    @elseif ($ticket->status === 'Sold Out')

                                        <x-admin.status-badge type="danger">
                                            Sold Out
                                        </x-admin.status-badge>

                                    @else

                                        <x-admin.status-badge type="warning">
                                            Closed
                                        </x-admin.status-badge>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="px-8 py-6 align-middle">
                                    <div class="flex items-center justify-center gap-3">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.tickets.edit', $ticket) }}"
                                            class="px-5 py-3 rounded-2xl border border-gray-200 hover:bg-black hover:text-white transition duration-300">
                                            Edit
                                        </a>

                                        {{-- Delete Form (Menggunakan class global delete-form) --}}
                                        <form class="delete-form" action="{{ route('admin.tickets.destroy', $ticket) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-5 py-3 rounded-2xl border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition duration-300">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $tickets->withQueryString()->links() }}
        </div>

    </div>

@endsection