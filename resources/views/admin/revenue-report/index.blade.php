@extends('layouts.admin')

@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')
@section('breadcrumb', 'Revenue Report')

@section('content')

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-14">

            <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                Stored Procedure Analytics
            </p>

            <h1 class="museum-title text-6xl font-light">
                Revenue Report
            </h1>

        </div>

        {{-- Filter --}}
        <div class="admin-card p-8 mb-10">

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">

                <div>
                    <label class="block text-sm mb-3 text-gray-500">
                        Start Date
                    </label>

                    <input type="date" name="start_date" value="{{ $startDate }}" class="admin-input">
                </div>

                <div>
                    <label class="block text-sm mb-3 text-gray-500">
                        End Date
                    </label>

                    <input type="date" name="end_date" value="{{ $endDate }}" class="admin-input">
                </div>

                <button type="submit" class="primary-button rounded-2xl px-6 py-4">
                    Generate Report
                </button>

            </form>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            {{-- Revenue --}}
            <div class="admin-card p-8">

                <p class="text-sm uppercase tracking-[0.2em] text-gray-400 mb-3">
                    Total Revenue
                </p>

                <h2 class="museum-title text-5xl">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h2>

            </div>

            {{-- Transactions --}}
            <div class="admin-card p-8">

                <p class="text-sm uppercase tracking-[0.2em] text-gray-400 mb-3">
                    Transactions
                </p>

                <h2 class="museum-title text-5xl">
                    {{ $totalTransactions }}
                </h2>

            </div>

            {{-- Tickets --}}
            <div class="admin-card p-8">

                <p class="text-sm uppercase tracking-[0.2em] text-gray-400 mb-3">
                    Tickets Sold
                </p>

                <h2 class="museum-title text-5xl">
                    {{ $totalTickets }}
                </h2>

            </div>

        </div>

        {{-- Table --}}
        <div class="admin-card overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="border-b border-gray-100">

                        <tr class="text-left">

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Visit Date
                            </th>

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Transactions
                            </th>

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Tickets Sold
                            </th>

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Revenue
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($reports as $report)

                            <tr class="border-b border-gray-100 hover:bg-black/5 transition">

                                <td class="px-8 py-6">
                                    {{ \Carbon\Carbon::parse($report->visit_date)->format('d M Y') }}
                                </td>

                                <td class="px-8 py-6">
                                    {{ $report->total_transactions }}
                                </td>

                                <td class="px-8 py-6">
                                    {{ $report->tickets_sold }}
                                </td>

                                <td class="px-8 py-6 font-medium">
                                    Rp {{ number_format($report->daily_revenue, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-20 text-gray-400">
                                    No revenue report data found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection