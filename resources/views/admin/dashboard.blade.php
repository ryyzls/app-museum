@extends('layouts.admin')

@section('title', 'Dashboard - Alphaseum')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <div class="space-y-10">

        {{-- HERO --}}
        <section class="admin-card p-10 relative overflow-hidden">

            <div
                class="absolute top-0 right-0 w-72 h-72 bg-gradient-to-br from-[#c9a96e]/20 to-transparent rounded-full blur-3xl">
            </div>

            <div class="relative z-10">
                <p class="uppercase tracking-[0.35em] text-xs text-gray-400 mb-5">
                    Administration Overview
                </p>

                <h1 class="museum-title text-6xl md:text-7xl font-light mb-6">
                    Welcome Back, Admin
                </h1>

                <p class="text-gray-500 max-w-2xl leading-relaxed">
                    Monitor exhibitions, artworks, ticket systems,
                    and museum ecosystem activity from one elegant dashboard.
                </p>
            </div>

        </section>

        {{-- STATS --}}
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            {{-- ARTWORKS --}}
            <div class="admin-card p-8 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-[11px] text-gray-400 mb-3">
                            Total Artworks
                        </p>
                        <h2 class="museum-title text-6xl font-light">
                            {{ $totalArtworks }}
                        </h2>
                    </div>
                    {{-- Ikon Premium pengganti Emoji --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                        style="background: rgba(201,169,110,0.12); color: var(--gold-dark);">
                        ▣
                    </div>
                </div>
                {{-- Quick Link dinamis pengganti teks hardcode --}}
                <a href="{{ route('admin.artworks.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#c9a96e] transition duration-300 group w-fit">
                    <span>Manage collection</span>
                    <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            {{-- ARTISTS --}}
            <div class="admin-card p-8 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-[11px] text-gray-400 mb-3">
                            Total Artists
                        </p>
                        <h2 class="museum-title text-6xl font-light">
                            {{ $totalArtists }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                        style="background: rgba(201,169,110,0.12); color: var(--gold-dark);">
                        ◈
                    </div>
                </div>
                <a href="{{ route('admin.artists.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#c9a96e] transition duration-300 group w-fit">
                    <span>View artist</span>
                    <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            {{-- EXHIBITIONS --}}
            <div class="admin-card p-8 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-[11px] text-gray-400 mb-3">
                            Exhibitions
                        </p>
                        <h2 class="museum-title text-6xl font-light">
                            {{ $totalExhibitions }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                        style="background: rgba(201,169,110,0.12); color: var(--gold-dark);">
                        ◩
                    </div>
                </div>
                <a href="{{ route('admin.exhibitions.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#c9a96e] transition duration-300 group w-fit">
                    <span>View exhibitions</span>
                    <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            {{-- TICKETS --}}
            <div class="admin-card p-8 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-[11px] text-gray-400 mb-3">
                            Tickets
                        </p>
                        <h2 class="museum-title text-6xl font-light">
                            {{ $totalTickets }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                        style="background: rgba(201,169,110,0.12); color: var(--gold-dark);">
                        ◌
                    </div>
                </div>
                <a href="{{ route('admin.tickets.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#c9a96e] transition duration-300 group w-fit">
                    <span>Manage tickets</span>
                    <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

        </section>

        {{-- QUICK ACTIONS --}}
        <section class="admin-card p-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div>
                    <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                        Quick Actions
                    </p>
                    <h2 class="museum-title text-5xl font-light">
                        Manage Ecosystem
                    </h2>
                </div>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.artworks.create') }}" class="primary-button px-7 py-4 rounded-2xl font-medium">
                        Add Artwork
                    </a>
                    <a href="{{ route('admin.artists.index') }}"
                        class="px-7 py-4 rounded-2xl border border-gray-200 bg-white/60 hover:bg-black hover:text-white transition duration-300">
                        View Artist
                    </a>
                    <a href="{{ route('admin.exhibitions.create') }}"
                        class="px-7 py-4 rounded-2xl border border-gray-200 bg-white/60 hover:bg-black hover:text-white transition duration-300">
                        Create Exhibition
                    </a>
                    <a href="{{ route('admin.tickets.create') }}"
                        class="px-7 py-4 rounded-2xl border border-gray-200 bg-white/60 hover:bg-black hover:text-white transition duration-300">
                        Create Ticket
                    </a>
                </div>
            </div>
        </section>

        {{-- RECENT ACTIVITY --}}
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- RECENT EXHIBITIONS --}}
            <div class="admin-card p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">
                            Recent Exhibitions
                        </p>
                        <h3 class="museum-title text-4xl">
                            Latest Updates
                        </h3>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach (\App\Models\Exhibition::latest()->take(4)->get() as $exhibition)
                        <div
                            class="flex items-center justify-between p-5 rounded-2xl bg-black/[0.02] hover:bg-black/[0.04] transition">
                            <div>
                                <h4 class="font-medium">
                                    {{ $exhibition->title }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ $exhibition->museum->name ?? 'Museum' }}
                                </p>
                            </div>
                            <div>
                                <span class="status-badge status-active">
                                    Live
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RECENT ARTWORKS --}}
            <div class="admin-card p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">
                            Recent Artworks
                        </p>
                        <h3 class="museum-title text-4xl">
                            Collection Feed
                        </h3>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach (\App\Models\Artwork::latest()->take(4)->get() as $artwork)
                        <div
                            class="flex items-center gap-5 p-5 rounded-2xl bg-black/[0.02] hover:bg-black/[0.04] transition overflow-hidden">
                            <img src="{{ Str::startsWith($artwork->image_url, ['http://', 'https://']) ? $artwork->image_url : asset('storage/' . $artwork->image_url) }}"
                                class="w-24 h-24 rounded-2xl object-cover flex-shrink-0">

                            <div class="min-w-0 flex-1">
                                <h4 class="font-medium line-clamp-2">
                                    {{ $artwork->title }}
                                </h4>
                                <p class="text-sm text-gray-500 line-clamp-2">
                                    {{ $artwork->artist->name ?? 'Unknown Artist' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </section>

    </div>

    {{-- GRAFIK PENJUALAN (CHART.JS) --}}
    <div class="mt-12 bg-white rounded-[32px] border border-gray-100 p-10 shadow-sm">
        <h3 class="text-xl font-normal mb-6">Grafik Pendapatan Pameran</h3>
        <div class="relative h-96 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="admin-card p-8 mt-10">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">

            <div>
                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">
                    Recent Activity
                </p>

                <h2 class="museum-title text-4xl">
                    Recent Transactions
                </h2>
            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="border-b border-gray-100">

                    <tr class="text-left">

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            User
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Exhibition
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Quantity
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Total
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($recentTransactions as $transaction)

                                <tr class="border-b border-gray-100 hover:bg-black/5 transition">

                                    {{-- User --}}
                                    <td class="px-6 py-5">
                                        {{ $transaction->user->name ?? 'Unknown' }}
                                    </td>

                                    {{-- Exhibition --}}
                                    <td class="px-6 py-5">
                                        {{ $transaction->ticket->exhibition->title ?? '-' }}
                                    </td>

                                    {{-- Quantity --}}
                                    <td class="px-6 py-5">
                                        {{ $transaction->quantity }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-5">
                                        Rp {{ number_format($transaction->total_price * 1000, 0, ',', '.') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-5">

                                        <span class="status-badge
                                                                {{ $transaction->payment_status == 'paid'
                        ? 'status-active'
                        : 'status-upcoming' }}">

                                            {{ ucfirst($transaction->payment_status) }}

                                        </span>

                                    </td>

                                </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-16 text-gray-400">
                                No recent transactions found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Script Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Mengambil data dari SQL View yang dikirim oleh Controller
            const rawData = @json($chartData);

            const labels = rawData.map(item => item.exhibition_title);
            const dataRevenue = rawData.map(item => item.total_revenue);

            new Chart(ctx, {
                type: 'bar', // Bisa diganti 'line' atau 'pie'
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pendapatan (Rp)',
                        data: dataRevenue,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)', // Warna hitam premium
                        borderColor: 'rgba(0, 0, 0, 1)',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

@endsection