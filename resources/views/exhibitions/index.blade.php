@extends('layouts.app')

@section('title', 'Exhibitions')

@section('content')

    <section class="min-h-screen bg-[#f5f5f3]">

        {{-- ================= HERO SECTION ================= --}}
        <section class="bg-[#f5f5f3]">

            {{-- Editorial Banner --}}
            <div class="relative overflow-hidden">

                {{-- Banner Image --}}
                <img src="https://api-www.louvre.fr/sites/default/files/2021-01/cour-napoleon-et-pyramide-de-nuit-nocturnes-du-samedi.jpg"
                    alt="Exhibitions Banner" class="w-full h-[260px] md:h-[340px] lg:h-[400px] object-cover">

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                {{-- Content --}}
                <div class="absolute inset-0 flex items-end">

                    <div class="max-w-7xl mx-auto w-full px-6 lg:px-10 pb-10 md:pb-14 lg:pb-16">

                        <div class="max-w-2xl">

                            {{-- Small Label --}}
                            <p class="uppercase tracking-[0.18em]
                                          text-[11px] md:text-sm
                                          text-white/80
                                          font-medium
                                          mb-4">

                                Museum Program

                            </p>

                            {{-- Main Title --}}
                            <h1 class="museum-title
                                           text-white
                                           text-4xl md:text-5xl lg:text-6xl
                                           font-light
                                           leading-[0.95]
                                           tracking-tight">

                                Exhibitions

                            </h1>

                            {{-- Description --}}
                            <p class="mt-5 max-w-xl
                                          text-white/75
                                          text-base
                                          leading-relaxed">
                                Explore curated exhibitions featuring timeless masterpieces,
                                cultural narratives, and historical collections from the museum ecosystem.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {{-- ================= EXHIBITION GRID ================= --}}
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-24">

            @if($exhibitions->count())

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14">

                    @foreach($exhibitions as $exhibition)

                        <a href="/exhibitions/{{ $exhibition->id }}" class="group block">

                            {{-- Exhibition Card --}}
                            <article class="relative overflow-hidden rounded-sm bg-black">

                                {{-- Banner --}}
                                <div class="relative overflow-hidden">

                                    <img src="{{ $exhibition->banner_image }}" alt="{{ $exhibition->title }}" class="h-[320px] md:h-[380px] lg:h-[420px]
                                                                   w-full
                                                                   object-cover
                                                                   transition duration-700 ease-out
                                                                   group-hover:scale-[1.02]">

                                    {{-- Gradient Overlay --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></div>

                                    {{-- Status --}}
                                    <div class="absolute top-6 left-6">

                                        <span class="px-4 py-2
                                                                         bg-white/90
                                                                         backdrop-blur-sm
                                                                         text-black
                                                                         text-[10px]
                                                                         uppercase
                                                                         tracking-[0.2em]
                                                                         font-medium">

                                            {{ $exhibition->status }}

                                        </span>

                                    </div>

                                    {{-- Content --}}
                                    <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-10 text-white">

                                        {{-- Museum --}}
                                        <p class="uppercase
                                                                      tracking-[0.25em]
                                                                      text-[10px]
                                                                      text-white/65
                                                                      mb-4">

                                            {{ $exhibition->museum->name }}

                                        </p>

                                        {{-- Title --}}
                                        <h2 class="museum-title
                                                                       text-3xl md:text-4xl
                                                                       leading-tight
                                                                       tracking-tight
                                                                       mb-4">

                                            {{ $exhibition->title }}

                                        </h2>

                                        {{-- Subtitle --}}
                                        @if($exhibition->subtitle)

                                            <p class="text-white/75
                                                                                  text-base
                                                                                  leading-relaxed
                                                                                  mb-6
                                                                                  max-w-xl">

                                                {{ $exhibition->subtitle }}

                                            </p>

                                        @endif

                                        {{-- Metadata --}}
                                        <div class="flex flex-wrap gap-5 text-sm text-white/60">

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

                            </article>

                        </a>

                    @endforeach

                </div>

                {{-- Pagination --}}
                <div class="mt-20">

                    {{ $exhibitions->links() }}

                </div>

            @else

                {{-- Empty State --}}
                <div class="text-center py-40">

                    <p class="uppercase
                                          tracking-[0.35em]
                                          text-xs
                                          text-gray-400
                                          mb-6">

                        Exhibition Space

                    </p>

                    <h2 class="museum-title
                                           text-5xl
                                           font-light
                                           mb-6
                                           tracking-tight">

                        No Exhibitions Available

                    </h2>

                    <p class="text-gray-500
                                          max-w-2xl
                                          mx-auto
                                          leading-relaxed">

                        Exhibition content will appear here once connected
                        to the museum ecosystem.

                    </p>

                </div>

            @endif

        </div>

    </section>

@endsection