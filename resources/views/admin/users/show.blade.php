@extends('layouts.admin')

@section('title', 'User Details - Alphaseum')
@section('breadcrumb', 'User Details')
@section('page-title', $user->name)

@section('content')

    <div class="space-y-10">

        {{-- USER PROFILE --}}
        <section class="admin-card p-10">

            <div class="flex items-center justify-between flex-wrap gap-6">

                <div>

                    <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                        User Information
                    </p>

                    <h1 class="museum-title text-5xl font-light mb-4">
                        {{ $user->name }}
                    </h1>

                    <div class="space-y-2 text-gray-500">

                        <p>
                            {{ $user->email }}
                        </p>

                        <p>
                            Role:
                            <span class="text-black font-medium">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>

                    </div>

                </div>

                <div class="grid grid-cols-2 gap-4">

                    {{-- TOTAL TRANSACTIONS --}}
                    <div class="bg-black/[0.03] rounded-3xl px-8 py-6 min-w-[180px]">

                        <p class="uppercase tracking-[0.3em] text-[10px] text-gray-400 mb-3">
                            Transactions
                        </p>

                        <h2 class="museum-title text-5xl font-light">
                            {{ $user->transactions->count() }}
                        </h2>

                    </div>

                    {{-- TOTAL REVIEWS --}}
                    <div class="bg-black/[0.03] rounded-3xl px-8 py-6 min-w-[180px]">

                        <p class="uppercase tracking-[0.3em] text-[10px] text-gray-400 mb-3">
                            Reviews
                        </p>

                        <h2 class="museum-title text-5xl font-light">
                            {{ $user->reviews->count() }}
                        </h2>

                    </div>

                </div>

            </div>

        </section>

        {{-- TRANSACTIONS --}}
        <section class="admin-card p-8">

            <div class="mb-8">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">
                    Purchase History
                </p>

                <h2 class="museum-title text-4xl">
                    Transactions
                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="border-b border-gray-100">

                        <tr class="text-left">

                            <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Transaction Code
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

                        @forelse($user->transactions as $transaction)

                                        <tr class="border-b border-gray-100 hover:bg-black/5 transition">

                                            <td class="px-6 py-5">
                                                {{ $transaction->transaction_code }}
                                            </td>

                                            <td class="px-6 py-5">
                                                {{ $transaction->ticket->exhibition->title ?? '-' }}
                                            </td>

                                            <td class="px-6 py-5">
                                                {{ $transaction->quantity }}
                                            </td>

                                            <td class="px-6 py-5">
                                                Rp {{ number_format($transaction->total_price * 1000, 0, ',', '.') }}
                                            </td>

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
                                    No transactions found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

        {{-- REVIEWS --}}
        <section class="admin-card p-8">

            <div class="mb-8">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-3">
                    Artwork Feedback
                </p>

                <h2 class="museum-title text-4xl">
                    Reviews
                </h2>

            </div>

            <div class="space-y-5">

                @forelse($user->reviews as $review)

                    <div class="bg-black/[0.03] rounded-3xl p-8">

                        <div class="flex items-center justify-between mb-4 flex-wrap gap-4">

                            <div>

                                <h3 class="text-xl font-medium">
                                    {{ $review->artwork->title ?? 'Artwork' }}
                                </h3>

                                <p class="text-gray-500 text-sm mt-1">
                                    Rating: {{ $review->rating }}/5
                                </p>

                            </div>

                            <div class="text-[#c9a96e] text-lg">

                                @for($i = 0; $i < $review->rating; $i++)
                                    ★
                                @endfor

                            </div>

                        </div>

                        <p class="text-gray-600 leading-relaxed">
                            {{ $review->comment }}
                        </p>

                    </div>

                @empty

                    <div class="text-center py-16 text-gray-400">
                        No reviews found.
                    </div>

                @endforelse

            </div>

        </section>

    </div>

@endsection