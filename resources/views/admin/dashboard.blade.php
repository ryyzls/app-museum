@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <section class="max-w-7xl mx-auto px-8 py-28">

        {{-- Header --}}
        <div class="mb-20">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

                Administration

            </p>

            <h1 class="museum-title text-6xl md:text-8xl font-light">

                Dashboard

            </h1>

        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

            {{-- Artworks --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition duration-300">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Total Artworks

                </p>

                <h2 class="museum-title text-6xl font-light">

                    {{ $totalArtworks }}

                </h2>

            </div>

            {{-- Artists --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition duration-300">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Total Artists

                </p>

                <h2 class="museum-title text-6xl font-light">

                    {{ $totalArtists }}

                </h2>

            </div>

            {{-- Exhibitions --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition duration-300">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Total Exhibitions

                </p>

                <h2 class="museum-title text-6xl font-light">

                    {{ $totalExhibitions }}

                </h2>

            </div>

            {{-- Tickets --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition duration-300">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Total Tickets

                </p>

                <h2 class="museum-title text-6xl font-light">

                    {{ $totalTickets }}

                </h2>

            </div>

            {{-- Transactions --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition duration-300">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Total Transactions

                </p>

                <h2 class="museum-title text-6xl font-light">

                    {{ $totalTransactions }}

                </h2>

            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="mt-20">

            <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-8">

                Quick Actions

            </p>

            <div class="flex flex-wrap gap-6">

                <a href="#" class="px-8 py-5 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">

                    Add Artwork

                </a>

                <a href="#"
                    class="px-8 py-5 bg-white border border-gray-200 rounded-2xl hover:bg-black hover:text-white transition duration-300">

                    Create Exhibition

                </a>

                <a href="#"
                    class="px-8 py-5 bg-white border border-gray-200 rounded-2xl hover:bg-black hover:text-white transition duration-300">

                    Manage Tickets

                </a>

            </div>

        </div>


    </section>

@endsection