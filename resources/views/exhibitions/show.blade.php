@extends('layouts.app')

@section('title', $exhibition->title)

@section('content')

    <section class="bg-white min-h-screen">

        {{-- HERO --}}
        <div class="relative h-[90vh] overflow-hidden">

            {{-- Banner --}}
            <img src="{{ $exhibition->banner_image }}" alt="{{ $exhibition->title }}"
                class="absolute inset-0 w-full h-full object-cover">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/60"></div>

            {{-- Content --}}
            <div class="relative z-10 h-full flex items-end">

                <div class="max-w-7xl mx-auto px-6 lg:px-10 pb-20 w-full text-white">

                    {{-- Status --}}
                    <div class="mb-6">

                        <span
                            class="px-5 py-2 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-sm uppercase tracking-[0.25em]">

                            {{ $exhibition->status }}

                        </span>

                    </div>

                    {{-- Title --}}
                    <h1 class="museum-title text-5xl md:text-7xl lg:text-8xl font-light leading-none max-w-5xl">

                        {{ $exhibition->title }}

                    </h1>

                    {{-- Subtitle --}}
                    @if($exhibition->subtitle)

                        <p class="mt-6 text-xl md:text-2xl text-white/80 max-w-3xl">

                            {{ $exhibition->subtitle }}

                        </p>

                    @endif

                    {{-- Info --}}
                    <div class="mt-10 flex flex-wrap gap-10 text-sm uppercase tracking-[0.2em] text-white/70">

                        <div>

                            {{ $exhibition->museum->name }}

                        </div>

                        <div>

                            {{ $exhibition->start_date->format('d M Y') }}
                            —
                            {{ $exhibition->end_date->format('d M Y') }}

                        </div>

                        <div>

                            {{ $exhibition->artworks->count() }}
                            Artworks

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- DESCRIPTION --}}
        <section class="max-w-5xl mx-auto px-6 lg:px-10 py-24">

            <p class="uppercase tracking-[0.35em] text-sm text-gray-400 mb-6">

                Curatorial Overview

            </p>

            <div class="grid lg:grid-cols-12 gap-16">

                <div class="lg:col-span-8">

                    <p class="text-2xl leading-relaxed text-gray-800 font-light">

                        {{ $exhibition->description }}

                    </p>

                </div>

                <div class="lg:col-span-4">

                    <div class="border-l border-gray-200 pl-8 space-y-8">

                        <div>

                            <p class="uppercase tracking-[0.2em] text-xs text-gray-400 mb-3">

                                Status

                            </p>

                            <p class="text-lg text-black">

                                {{ $exhibition->status }}

                            </p>

                        </div>

                        <div>

                            <p class="uppercase tracking-[0.2em] text-xs text-gray-400 mb-3">

                                Museum

                            </p>

                            <p class="text-lg text-gray-800">

                                {{ $exhibition->museum->name }}

                            </p>

                        </div>

                        <div>

                            <p class="uppercase tracking-[0.2em] text-xs text-gray-400 mb-3">

                                Exhibition Period

                            </p>

                            <p class="text-lg text-gray-800">

                                {{ $exhibition->start_date->format('d M Y') }}
                                —
                                {{ $exhibition->end_date->format('d M Y') }}

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>



        {{-- AVAILABLE TICKETS --}}
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-24">

            <div class="mb-16">

                <p class="uppercase tracking-[0.35em] text-sm text-gray-400 mb-5">

                    Museum Access

                </p>

                <h2 class="museum-title text-5xl md:text-6xl font-light">

                    Available Tickets

                </h2>

                <p class="mt-5 max-w-2xl text-gray-500 leading-relaxed">

                    Reserve museum access for your selected exhibition experience.
                    Ticket availability is limited and subject to exhibition schedule.

                </p>

            </div>

            @if($exhibition->tickets->count())

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    @foreach($exhibition->tickets as $ticket)

                        <div
                            class="border border-gray-200 rounded-3xl p-8 hover:border-black hover:-translate-y-1 hover:shadow-2xl transition duration-300">

                            {{-- Ticket Type --}}
                            <div class="mb-10">

                                <p class="uppercase tracking-[0.25em] text-xs text-gray-400 mb-4">

                                    Ticket Type

                                </p>

                                <h3 class="museum-title text-4xl">

                                    {{ $ticket->ticket_type }}

                                </h3>

                            </div>

                            {{-- Price --}}
                            <div class="mb-10">

                                <p class="uppercase tracking-[0.25em] text-xs text-gray-400 mb-3">

                                    Price

                                </p>

                                <p class="text-5xl font-light text-gray-900">

                                    €{{ number_format($ticket->price, 0) }}

                                </p>

                            </div>

                            {{-- Info --}}
                            <div class="space-y-6 mb-10">

                                {{-- Visit Date --}}
                                <div class="flex justify-between items-center">

                                    <span class="text-gray-500">

                                        Visit Date

                                    </span>

                                    <span class="text-gray-800">

                                        {{ $ticket->visit_date->format('d M Y') }}

                                    </span>

                                </div>

                                {{-- Remaining Quota --}}
                                @if($ticket->status !== 'Closed')

                                    <div class="flex justify-between items-center">

                                        <span class="text-gray-500">

                                            Remaining

                                        </span>

                                        <span class="text-gray-800">

                                            {{ $ticket->available_quota }}

                                        </span>

                                    </div>

                                @endif

                                {{-- Status --}}
                                <div class="flex justify-between items-center">

                                    <span class="text-gray-500">

                                        Status

                                    </span>

                                    <span class="
                                                            px-3 py-1 rounded-full text-xs uppercase tracking-[0.15em]

                                                            @if($ticket->status === 'Available')
                                                                bg-green-100 text-green-700

                                                            @elseif($ticket->status === 'Sold Out')
                                                                bg-red-100 text-red-700

                                                            @else
                                                                bg-gray-200 text-gray-700
                                                            @endif
                                                        ">

                                        {{ $ticket->status }}

                                    </span>

                                </div>

                            </div>

                            {{-- CTA --}}
                            @if($ticket->status === 'Available')

                                <a href="{{ route('tickets.checkout', $ticket->id) }}"
                                    class="block w-full py-4 rounded-full bg-black text-white uppercase tracking-[0.2em] text-sm text-center hover:bg-gray-900 hover:scale-[1.02] transition">

                                    Reserve Ticket

                                </a>

                            @elseif($ticket->status === 'Sold Out')

                                <button disabled
                                    class="w-full py-4 rounded-full bg-red-100 text-red-700 uppercase tracking-[0.2em] text-sm cursor-not-allowed">

                                    Sold Out

                                </button>

                            @else

                                <button disabled
                                    class="w-full py-4 rounded-full bg-gray-200 text-gray-600 uppercase tracking-[0.2em] text-sm cursor-not-allowed">

                                    Exhibition Ended

                                </button>

                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                <div class="text-center py-24 border border-dashed border-gray-300 rounded-3xl">

                    <h2 class="museum-title text-4xl mb-5">

                        No Tickets Available

                    </h2>

                    <p class="text-gray-500">

                        Ticket information for this exhibition is currently unavailable.

                    </p>

                </div>

            @endif

        </section>



@endsection