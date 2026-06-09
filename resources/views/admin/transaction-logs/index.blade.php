@extends('layouts.admin')

@section('title', 'Transaction Logs')
@section('page-title', 'Transaction Logs')
@section('breadcrumb', 'Transaction Logs')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-14">
        <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
            Trigger Activity
        </p>

        <h1 class="museum-title text-6xl font-light">
            Transaction Logs
        </h1>
    </div>

    {{-- Table --}}
    <div
        class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px]
        overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="border-b border-gray-100">

                    <tr class="text-left">

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Transaction ID
                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Old Status
                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                            New Status
                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Changed At
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($logs as $log)

                        <tr class="border-b border-gray-100 hover:bg-black/5 transition duration-200">

                            <td class="px-8 py-6">
                                #{{ $log->transaction_id }}
                            </td>

                            <td class="px-8 py-6">
                                {{ $log->old_status ?? '-' }}
                            </td>

                            <td class="px-8 py-6">
                                {{ $log->new_status ?? '-' }}
                            </td>

                            <td class="px-8 py-6">
                                {{ \Carbon\Carbon::parse($log->changed_at)->format('d M Y H:i') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center py-20 text-gray-400">
                                No transaction logs found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $logs->links() }}
    </div>

</div>

@endsection