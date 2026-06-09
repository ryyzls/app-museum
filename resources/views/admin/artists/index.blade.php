@extends('layouts.admin')

@section('title', 'Artists')
@section('page-title', 'Artists')
@section('breadcrumb', 'Artists')

@section('content')

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-14">
            <div>
                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">
                    Artist Directory
                </p>

                <h1 class="museum-title text-6xl font-light">
                    Artists
                </h1>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px]
                overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

            <div class="overflow-x-auto w-full">

                <table class="w-full min-w-[900px]">

                    <thead class="border-b border-gray-100">
                        <tr class="text-left">
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Artist
                            </th>

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Biography
                            </th>

                            <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">
                                Total Artworks
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($artists as $artist)

                            <tr class="border-b border-gray-100 hover:bg-black/5 transition duration-200">

                                {{-- Artist --}}
                                <td class="px-8 py-6 w-[260px]">

                                    <h2 class="text-lg font-medium">
                                        {{ $artist->name }}
                                    </h2>

                                </td>

                                {{-- Biography --}}
                                <td class="px-8 py-6">

                                    <p class="text-gray-500 leading-relaxed">
                                        {{ $artist->bio ?? 'No biography available.' }}
                                    </p>

                                </td>

                                {{-- Total Artworks --}}
                                <td class="px-8 py-6 whitespace-nowrap">

                                    {{ $artist->artworks_count }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $artists->links() }}
        </div>

    </div>

@endsection