```php
@extends('layouts.app')

@section('title', 'Exhibitions')

@section('content')

    <section class="min-h-screen bg-white">

        {{-- Hero --}}
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-24 pb-20">

            <p class="uppercase tracking-[0.35em] text-sm text-gray-500 mb-5">

                Museum Program

            </p>

            <h1 class="museum-title text-5xl md:text-7xl lg:text-8xl font-light leading-none">

                Exhibitions

            </h1>

            <p class="mt-8 max-w-2xl text-gray-600 text-lg leading-relaxed">

                Explore curated exhibitions featuring timeless masterpieces,
                cultural narratives, and historical collections from the museum ecosystem.

            </p>

        </div>

        {{-- Exhibition Grid --}}
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pb-28">

            @if($exhibitions->count())

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    @foreach($exhibitions as $exhibition)

                        <a href="/exhibitions/{{ $exhibition->id }}" class="group block">

                            {{-- Banner --}}
                            <div class="relative overflow-hidden rounded-3xl">

                                <img src="{{ $exhibition->banner_image }}" alt="{{ $exhibition->title }}"
                                    class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">

                                {{-- Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                {{-- Status --}}
                                <div class="absolute top-6 left-6">

                                    <span
                                        class="px-4 py-2 rounded-full bg-white/90 backdrop-blur text-xs uppercase tracking-[0.2em]">

                                        {{ $exhibition->status }}

                                    </span>

                                </div>

                                {{-- Content --}}
                                <div class="absolute bottom-0 p-8 text-white">

                                    <p class="uppercase tracking-[0.25em] text-xs mb-4 text-white/70">

                                        {{ $exhibition->museum->name }}

                                    </p>

                                    <h2 class="museum-title text-4xl md:text-5xl leading-tight mb-4">

                                        {{ $exhibition->title }}

                                    </h2>

                                    @if($exhibition->subtitle)

                                        <p class="text-white/80 text-lg mb-5">

                                            {{ $exhibition->subtitle }}

                                        </p>

                                    @endif

                                    <div class="flex flex-wrap gap-6 text-sm text-white/70">

                                        <span>

                                            {{ $exhibition->start_date->format('d M Y') }}
                                            —
                                            {{ $exhibition->end_date->format('d M Y') }}

                                        </span>

                                        <span>

                                            {{ $exhibition->artworks->count() }}
                                            artworks

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

                {{-- Pagination --}}
                <div class="mt-20">

                    {{ $exhibitions->links() }}

                </div>

            @else

                <div class="text-center py-40">

                    <p class="uppercase tracking-[0.35em] text-sm text-gray-400 mb-6">

                        Exhibition Space

                    </p>

                    <h2 class="museum-title text-5xl font-light mb-6">

                        No Exhibitions Available

                    </h2>

                    <p class="text-gray-500 max-w-2xl mx-auto">

                        Exhibition content will appear here once connected
                        to the museum ecosystem.

                    </p>

                </div>

            @endif

        </div>

    </section>

@endsection
```