@extends('layouts.admin')

@section('title', 'Manage Exhibitions')
@section('page-title', 'Exhibitions')
@section('breadcrumb', 'Exhibitions')

@section('content')
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-14">
            <div>
                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                    Exhibition Management
                </p>
                <h1 class="museum-title text-6xl font-light">
                    Exhibitions
                </h1>
            </div>
            <a href="{{ route('admin.exhibitions.create') }}"
                class="px-8 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">
                Create Exhibition
            </a>
        </div>

        {{-- Table Card --}}
        <div
            class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

            {{-- Wrapper khusus agar scrollbar muncul di bawah tabel --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[1050px]">
                    <thead class="border-b border-gray-100">
                        <tr class="text-left">
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Exhibition</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Museum</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Status</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Artworks</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Duration</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exhibitions as $exhibition)
                            <tr class="border-b border-gray-100 hover:bg-black/5 transition duration-200">

                                {{-- Exhibition --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <img src="{{ str_starts_with($exhibition->banner_image, 'http') ? $exhibition->banner_image : asset('storage/' . $exhibition->banner_image) }}"
                                            class="w-24 h-20 object-cover rounded-2xl flex-shrink-0"
                                            alt="{{ $exhibition->title }}">
                                        <div>
                                            <h2 class="text-lg font-medium">{{ $exhibition->title }}</h2>
                                            <p class="text-sm text-gray-500 mt-1">{{ $exhibition->subtitle }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Museum --}}
                                <td class="px-8 py-6">
                                    {{ $exhibition->museum->name ?? '-' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-8 py-6">
                                    @if ($exhibition->computed_status == 'Current')
                                        <x-admin.status-badge type="success">Current</x-admin.status-badge>
                                    @elseif ($exhibition->computed_status == 'Upcoming')
                                        <x-admin.status-badge type="warning">Upcoming</x-admin.status-badge>
                                    @else
                                        <x-admin.status-badge type="danger">Past</x-admin.status-badge>
                                    @endif
                                </td>

                                {{-- Artworks Count --}}
                                <td class="px-8 py-6">
                                    {{ $exhibition->artworks->count() }}
                                </td>

                                {{-- Duration --}}
                                <td class="px-8 py-6">
                                    {{ \Carbon\Carbon::parse($exhibition->start_date)->format('d M Y') }}
                                    —
                                    {{ \Carbon\Carbon::parse($exhibition->end_date)->format('d M Y') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.exhibitions.edit', $exhibition) }}"
                                            class="px-5 py-3 rounded-2xl border border-gray-200 hover:bg-black hover:text-white transition duration-300">
                                            Edit
                                        </a>

                                        {{-- Delete Form --}}
                                        <form class="delete-form" action="{{ route('admin.exhibitions.destroy', $exhibition) }}"
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
            </div> {{-- Akhir dari wrapper overflow-x-auto --}}
        </div> {{-- Akhir dari card --}}

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $exhibitions->links() }}
        </div>

    </div>

@endsection