@extends('layouts.app')

@section('title', $exhibition->title)

@section('content')

    <section class="max-w-7xl mx-auto px-8 py-28">

        {{-- Header --}}
        <div class="mb-20">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

                Exhibition

            </p>

            <h1 class="museum-title text-6xl md:text-8xl font-light mb-8">

                {{ $exhibition->title }}

            </h1>

            <div class="flex flex-wrap gap-10 text-gray-600">

                <div>

                    <p class="uppercase text-xs tracking-[0.2em] mb-2">

                        Date

                    </p>

                    <p>

                        {{ $exhibition->start_date->format('d M Y') }}
                        —
                        {{ $exhibition->end_date->format('d M Y') }}

                    </p>

                </div>

                <div>

                    <p class="uppercase text-xs tracking-[0.2em] mb-2">

                        Museum

                    </p>

                    <p>

                        {{ $exhibition->museum->name }}

                    </p>

                </div>

                <div>

                    <p class="uppercase text-xs tracking-[0.2em] mb-2">

                        Artworks

                    </p>

                    <p>

                        {{ $exhibition->artworks->count() }}

                    </p>

                </div>

            </div>

        </div>

        {{-- Artworks --}}
        <div>

            <div class="mb-12">

                <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

                    Featuring

                </p>

                <h2 class="museum-title text-5xl font-light">

                    Artworks Collection

                </h2>

            </div>

            @if($exhibition->artworks->count())

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

                    @foreach($exhibition->artworks as $artwork)

                        <a href="/artworks/{{ $artwork->id }}" class="group">

                            <div class="overflow-hidden">

                                <img src="{{ $artwork->image_url }}" onerror="this.src='/images/fallback.jpg'"
                                    class="h-[400px] w-full object-cover transition duration-500">

                            </div>

                            <div class="pt-6">

                                <h3 class="museum-title text-3xl">

                                    {{ $artwork->title }}

                                </h3>

                                <p class="mt-2 text-gray-500">

                                    {{ $artwork->artist->name }}

                                </p>

                            </div>

                        </a>

                    @endforeach

                </div>

            @else

                <div class="text-center py-20">

                    <h2 class="museum-title text-4xl mb-4">

                        No Artworks Connected

                    </h2>

                    <p class="text-gray-500">

                        Exhibition content will appear here later.

                    </p>

                </div>

            @endif

        </div>

    </section>

@endsection