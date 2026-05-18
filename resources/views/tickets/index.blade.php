@extends('layouts.app')

@section('title', 'Available Tickets')

@section('content')

    <section class="max-w-7xl mx-auto px-8 py-28">

        {{-- Header --}}
        <div class="mb-24">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

                Reservation Hub

            </p>

            <h1 class="museum-title text-6xl md:text-8xl font-light">

                Available Tickets

            </h1>

            <p class="mt-8 max-w-2xl text-gray-500 leading-relaxed">

                Discover and reserve tickets for current museum exhibitions.

            </p>

        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-col md:flex-row gap-6 mb-16">

            {{-- Status --}}
            <select name="status" class="border border-gray-300 px-6 py-4 rounded-full">

                <option value="">

                    All Status

                </option>

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

            {{-- Ticket Type --}}
            <select name="type" class="border border-gray-300 px-6 py-4 rounded-full">

                <option value="">

                    All Ticket Types

                </option>

                <option value="Regular" {{ request('type') == 'Regular' ? 'selected' : '' }}>

                    Regular

                </option>

                <option value="VIP" {{ request('type') == 'VIP' ? 'selected' : '' }}>

                    VIP

                </option>

                <option value="Student" {{ request('type') == 'Student' ? 'selected' : '' }}>

                    Student

                </option>

            </select>

            {{-- Button --}}
            <button type="submit"
                class="border border-black px-8 py-4 uppercase tracking-[0.3em] hover:bg-black hover:text-white transition duration-500">

                Apply Filters

            </button>

        </form>



        {{-- Ticket Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            @foreach($tickets as $ticket)

                <div
                    class="bg-white border border-gray-200 rounded-[36px] overflow-hidden hover:-translate-y-2 transition duration-500">

                    {{-- Exhibition Image --}}
                    <div class="relative h-[320px] overflow-hidden">

                        <img src="{{ $ticket->exhibition->banner_image }}" alt="{{ $ticket->exhibition->title }}"
                            class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                        {{-- Status --}}
                        <div class="absolute top-6 right-6">

                            @if($ticket->status === 'Available')

                                <span class="bg-green-500 text-white text-xs px-4 py-2 rounded-full uppercase tracking-[0.2em]">

                                    Available

                                </span>

                            @elseif($ticket->status === 'Sold Out')

                                <span class="bg-red-500 text-white text-xs px-4 py-2 rounded-full uppercase tracking-[0.2em]">

                                    Sold Out

                                </span>

                            @else

                                <span class="bg-gray-700 text-white text-xs px-4 py-2 rounded-full uppercase tracking-[0.2em]">

                                    Closed

                                </span>

                            @endif

                        </div>

                        {{-- Exhibition Title --}}
                        <div class="absolute bottom-8 left-8 right-8 text-white">

                            <h2 class="museum-title text-4xl leading-tight mb-3">

                                {{ $ticket->exhibition->title }}

                            </h2>

                            <p class="text-white/80">

                                {{ $ticket->exhibition->subtitle }}

                            </p>

                        </div>

                    </div>

                    {{-- Content --}}
                    <div class="p-10">

                        {{-- Ticket Type --}}
                        <div class="mb-8">

                            <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">

                                Ticket Type

                            </p>

                            <h3 class="text-3xl font-light">

                                {{ $ticket->ticket_type }}

                            </h3>

                        </div>

                        {{-- Info --}}
                        <div class="space-y-5 mb-10">

                            <div class="flex justify-between border-b pb-4">

                                <span class="text-gray-500">

                                    Visit Date

                                </span>

                                <span>

                                    {{ $ticket->visit_date->format('d M Y') }}

                                </span>

                            </div>

                            <div class="flex justify-between border-b pb-4">

                                <span class="text-gray-500">

                                    Remaining Quota

                                </span>

                                <span>

                                    {{ $ticket->available_quota }}

                                </span>

                            </div>

                        </div>

                        {{-- Price --}}
                        <div class="mb-10">

                            <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                                Price

                            </p>

                            <h2 class="museum-title text-5xl font-light">

                                €{{ number_format($ticket->price, 0) }}

                            </h2>

                        </div>

                        {{-- Button --}}
                        <a href="{{ route('tickets.checkout', $ticket->id) }}"
                            class="inline-flex items-center justify-center w-full border border-black py-5 uppercase tracking-[0.3em] hover:bg-black hover:text-white transition duration-500">

                            Reserve Ticket

                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    </section>

@endsection