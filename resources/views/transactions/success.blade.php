@extends('layouts.app')

@section('title', 'Reservation Success')

@section('content')

    <section class="relative min-h-screen overflow-hidden bg-black">

        {{-- Background Image --}}
        <div class="absolute inset-0">

            <img src="{{ $transaction->ticket->exhibition->banner_image }}"
                alt="{{ $transaction->ticket->exhibition->title }}" class="w-full h-full object-cover">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/70"></div>

        </div>

        {{-- Content --}}
        <div class="relative z-10 max-w-5xl mx-auto px-6 py-24">

            {{-- Heading --}}
            <div class="text-center mb-20">

                <p class="uppercase tracking-[0.4em] text-sm text-gray-300 mb-6">

                    Reservation Confirmed

                </p>

                <h1 class="museum-title text-white text-5xl md:text-7xl leading-tight mb-8">

                    Your Reservation
                    Has Been Successfully Created

                </h1>

                <p class="text-gray-300 text-lg max-w-2xl mx-auto leading-relaxed">

                    Thank you for reserving your museum experience through
                    Alphaseum.

                </p>

            </div>

            {{-- Transaction Card --}}
            <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-[40px] p-10 md:p-14 shadow-2xl">

                <div class="grid md:grid-cols-2 gap-14 items-start">

                    {{-- LEFT --}}
                    <div>

                        <img src="{{ $transaction->ticket->exhibition->banner_image }}"
                            alt="{{ $transaction->ticket->exhibition->title }}"
                            class="w-full h-[280px] object-cover rounded-3xl mb-10">

                        <h2 class="museum-title text-white text-4xl mb-4">

                            {{ $transaction->ticket->exhibition->title }}

                        </h2>

                        <p class="text-gray-300 leading-relaxed">

                            {{ $transaction->ticket->exhibition->subtitle }}

                        </p>

                    </div>

                    {{-- RIGHT --}}
                    <div class="space-y-7">

                        <div class="flex justify-between items-center">

                            <span class="text-gray-300">

                                Transaction Code

                            </span>

                            <span class="text-white">

                                {{ $transaction->transaction_code }}

                            </span>

                        </div>

                        <div class="flex justify-between items-center">

                            <span class="text-gray-300">

                                Ticket Type

                            </span>

                            <span class="text-white">

                                {{ $transaction->ticket->ticket_type }}

                            </span>

                        </div>

                        <div class="flex justify-between items-center">

                            <span class="text-gray-300">

                                Quantity

                            </span>

                            <span class="text-white">

                                {{ $transaction->quantity }}

                            </span>

                        </div>

                        <div class="flex justify-between items-center">

                            <span class="text-gray-300">

                                Payment Method

                            </span>

                            <span class="text-white">

                                {{ $transaction->payment_method }}

                            </span>

                        </div>

                        <div class="flex justify-between items-center">

                            <span class="text-gray-300">

                                Payment Status

                            </span>

                            <span
                                class="px-3 py-1 rounded-full bg-yellow-400/20 text-yellow-300 text-xs uppercase tracking-[0.15em]">

                                {{ $transaction->payment_status }}

                            </span>

                        </div>

                        {{-- Total --}}
                        <div class="border-t border-white/20 pt-8 mt-8">

                            <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-5">

                                Total Payment

                            </p>

                            <h2 class="text-6xl md:text-7xl font-light text-white">

                                €{{ number_format($transaction->total_price, 0) }}

                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            {{-- CTA --}}
            <div class="text-center mt-14">

                <a href="{{ route('exhibitions.index') }}"
                    class="inline-flex items-center px-10 py-5 rounded-full bg-white text-black uppercase tracking-[0.2em] text-sm hover:bg-gray-200 transition">

                    Explore More Exhibitions

                </a>


                <a href="{{ route('transactions.download-ticket', $transaction->id) }}"
                    class="inline-flex items-center px-10 py-5 rounded-full border border-white text-white uppercase tracking-[0.2em] text-sm hover:bg-white hover:text-black transition ml-4">

                    Download E-Ticket

                </a>


            </div>

        </div>

    </section>

@endsection