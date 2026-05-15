@extends('layouts.app')

@section('title', 'Artworks')

@section('content')

    {{-- Hero Section --}}
    <section class="relative h-[70vh] overflow-hidden">

        <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=2070&auto=format&fit=crop"
            onerror="this.src='/images/fallback.jpg'" class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 h-full flex items-center">

            <div class="max-w-7xl mx-auto px-8 text-white">

                <p class="uppercase tracking-[0.4em] text-sm mb-6">
                    Museum Collection
                </p>

                <h1 class="museum-title text-6xl md:text-8xl font-light leading-none max-w-4xl">
                    Discover
                    Timeless Artworks
                </h1>

            </div>

        </div>

    </section>

    {{-- Artwork Collection --}}
    <section class="max-w-7xl mx-auto px-8 py-24">

        <div class="flex justify-between items-end mb-16">

            <div>

                <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-3">
                    Collection
                </p>

                <h2 class="museum-title text-5xl font-light">
                    Featured Artworks
                </h2>

            </div>

            <a href="/artworks/create"
                class="border border-black px-6 py-3 uppercase tracking-[0.2em] text-sm hover:bg-black hover:text-white transition duration-300">

                Add Artwork

            </a>

        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

            @if($artworks->count())


                @foreach($artworks as $artwork)

                    <div class="group relative overflow-hidden transition-all duration-500 hover:-translate-y-2">

                        {{-- Image --}}
                        <div class="relative overflow-hidden bg-gray-200">

                            <img src="{{ $artwork->image_url }}" onerror="this.src='{{ asset('images/artworks/fallback.jpg') }}'"
                                class="h-[500px] w-full object-cover transition duration-500" alt="{{ $artwork->title }}">

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-70 transition duration-500">
                            </div>

                            <div
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.10),transparent_70%)]">
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="pt-6">

                            {{-- Category --}}
                            <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-3">

                                {{ $artwork->category->name }}

                            </p>

                            {{-- Title --}}
                            <h3 class="museum-title text-3xl font-light mb-3">

                                {{ $artwork->title }}

                            </h3>

                            {{-- Artist --}}
                            <p class="text-gray-700 mb-2">

                                by {{ $artwork->artist->name }}

                            </p>

                            {{-- Museum --}}
                            <p class="text-sm text-gray-500 mb-5">

                                {{ $artwork->museum->name }}

                            </p>

                            {{-- Description --}}
                            <p class="text-gray-600 leading-relaxed line-clamp-3">

                                {{ $artwork->description }}

                            </p>

                            {{-- Action --}}
                            <div class="mt-6">

                                <a href="/artworks/{{ $artwork->id }}"
                                    class="uppercase tracking-[0.2em] text-sm border-b border-black pb-1">

                                    View Details

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            @else

                <div class="col-span-full text-center py-32">

                    <h2 class="museum-title text-5xl font-light mb-6">
                        No Artworks Available
                    </h2>

                    <p class="text-gray-500 text-lg">
                        The collection is currently empty.
                    </p>

                </div>

            @endif

        </div>

        <div class="mt-20">
            {{ $artworks->links() }}
        </div>

    </section>

@endsection