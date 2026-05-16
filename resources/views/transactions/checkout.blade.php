@extends('layouts.app')

@section('title', 'Ticket Checkout')

@section('content')

    <section class="min-h-screen bg-white">

        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-24">

            <div class="grid lg:grid-cols-2 gap-20 items-start">

                {{-- LEFT --}}
                <div>


                    {{-- Exhibition Image --}}
                    <div class="mb-10 overflow-hidden rounded-3xl">

                        <img src="{{ $ticket->exhibition->banner_image }}" alt="{{ $ticket->exhibition->title }}"
                            class="w-full h-[350px] object-cover">

                    </div>


                    <p class="uppercase tracking-[0.35em] text-sm text-gray-400 mb-5">

                        Reservation

                    </p>

                    <h1 class="museum-title text-5xl md:text-6xl leading-tight mb-8">

                        {{ $ticket->exhibition->title }}

                    </h1>

                    <p class="text-gray-600 text-lg leading-relaxed mb-12">

                        {{ $ticket->exhibition->subtitle }}

                    </p>

                    {{-- Ticket Info --}}
                    <div class="space-y-6 border-t border-gray-200 pt-10">

                        <div class="flex justify-between">

                            <span class="text-gray-500">

                                Ticket Type

                            </span>

                            <span class="text-gray-900">

                                {{ $ticket->ticket_type }}

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500">

                                Visit Date

                            </span>

                            <span class="text-gray-900">

                                {{ $ticket->visit_date->format('d M Y') }}

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500">

                                Available Quota

                            </span>

                            <span class="text-gray-900">

                                {{ $ticket->available_quota }}

                            </span>

                        </div>

                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="border border-gray-200 rounded-3xl p-10">

                    <form action="{{ route('tickets.reserve', $ticket->id) }}" method="POST">

                        @csrf

                        <div class="mb-10">

                            <p class="uppercase tracking-[0.25em] text-xs text-gray-400 mb-4">

                                Ticket Price

                            </p>

                            <h2 class="text-6xl font-light">

                                €{{ number_format($ticket->price, 0) }}

                            </h2>

                        </div>

                        {{-- Quantity --}}
                        <div class="mb-10">

                            <label class="block text-sm uppercase tracking-[0.2em] text-gray-400 mb-4">

                                Quantity

                            </label>

                            <input id="quantity" type="number" name="quantity" min="1" max="{{ $ticket->available_quota }}"
                                value="1" required
                                class="w-full border border-gray-300 rounded-2xl px-6 py-4 text-lg focus:outline-none focus:border-black">

                        </div>


                        {{-- Live Total --}}
                        <div class="mb-10">

                            <p class="uppercase tracking-[0.25em] text-xs text-gray-400 mb-4">

                                Total Payment

                            </p>

                            <h2 id="live-total" class="text-5xl font-light text-gray-900">

                                €{{ number_format($ticket->price, 0) }}

                            </h2>

                        </div>



                        {{-- Payment --}}
                        <div class="mb-12">

                            <label class="block text-sm uppercase tracking-[0.2em] text-gray-400 mb-4">

                                Payment Method

                            </label>

                            <select name="payment_method" required
                                class="w-full border border-gray-300 rounded-2xl px-6 py-4 text-lg focus:outline-none focus:border-black">

                                <option value="Credit Card">

                                    Credit Card

                                </option>

                                <option value="Bank Transfer">

                                    Bank Transfer

                                </option>

                                <option value="Digital Wallet">

                                    Digital Wallet

                                </option>

                            </select>

                        </div>

                        <button type="submit"
                            class="w-full py-5 rounded-full bg-black text-white uppercase tracking-[0.2em] text-sm hover:bg-gray-900 transition">

                            Complete Reservation

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </section>

    {{-- live quantity update --}}
    <script>

        const quantityInput = document.getElementById('quantity');

        const liveTotal = document.getElementById('live-total');

        const ticketPrice = {{ $ticket->price }};

        quantityInput.addEventListener('input', function () {

            let quantity = parseInt(this.value) || 1;

            let total = quantity * ticketPrice;

            liveTotal.innerText = '€' + total;

        });

    </script>



@endsection