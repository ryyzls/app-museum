@extends('layouts.app')

@section('title','Museum')

@section('content')

{{-- Hero --}}
<section class="relative h-screen overflow-hidden">

    <img
        src="https://images.unsplash.com/photo-1566127444979-b3d2b654e3d7?q=80&w=1974&auto=format&fit=crop"
        class="absolute inset-0 w-full h-full object-cover"
    >

    <div class="absolute inset-0 bg-black/55"></div>

    <div class="relative z-10 h-full flex items-center">

        <div class="max-w-7xl mx-auto px-8 text-white">

            <p class="uppercase tracking-[0.4em] text-sm mb-6">
                Digital Museum Experience
            </p>

            <h1 class="museum-title text-6xl md:text-8xl font-light max-w-5xl leading-none">

                Discover Timeless
                Art & Exhibitions

            </h1>

            <p class="mt-8 text-lg max-w-xl text-gray-200 leading-relaxed">

                Explore curated collections and immersive exhibitions
                from around the world.

            </p>

            <div class="mt-10 flex gap-6">

                <a href="/artworks"
                    class="bg-white text-black px-8 py-4 uppercase tracking-[0.2em] text-sm hover:bg-gray-200 transition">

                    Explore Collection

                </a>

                <a href="#"
                    class="border border-white px-8 py-4 uppercase tracking-[0.2em] text-sm hover:bg-white hover:text-black transition">

                    Buy Tickets

                </a>

            </div>

        </div>

    </div>

</section>

{{-- Curated Journey --}}
<section class="max-w-7xl mx-auto px-8 py-32">

    <div class="mb-20">

        <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

            Explore

        </p>

        <h2 class="museum-title text-5xl md:text-7xl font-light max-w-3xl leading-tight">

            Begin Your Journey
            Through Art

        </h2>

    </div>

    <div class="space-y-12">

        {{-- Item --}}
        <a href="/artworks"
           class="group flex justify-between items-center border-b border-gray-200 pb-10">

            <div>

                <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-4">

                    Renaissance

                </p>

                <h3 class="museum-title text-3xl md:text-5xl font-light">

                    Masters of History

                </h3>

                <p class="mt-4 text-gray-500 max-w-xl">

                    Discover legendary masterpieces that shaped artistic history.

                </p>

            </div>

            <div class="flex items-center gap-4">

                <span
                    class="uppercase tracking-[0.2em] text-sm opacity-0 group-hover:opacity-100 transition duration-500">

                    Explore

                </span>

                <span
                    class="text-3xl transition duration-500 group-hover:translate-x-3">

                    →

                </span>

            </div>

        </a>

        {{-- Item --}}
        <a href="/artworks"
           class="group flex justify-between items-center border-b border-gray-200 pb-10">

            <div>

                <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-4">

                    Contemporary

                </p>

                <h3 class="museum-title text-3xl md:text-5xl font-light">

                    Echoes of Humanity

                </h3>

                <p class="mt-4 text-gray-500 max-w-xl">

                    Experience modern artistic expression and emotion.

                </p>

            </div>

            <div class="flex items-center gap-4">

                <span
                    class="uppercase tracking-[0.2em] text-sm opacity-0 group-hover:opacity-100 transition duration-500">

                    Explore

                </span>

                <span
                    class="text-3xl transition duration-500 group-hover:translate-x-3">

                    →

                </span>

            </div>

        </a>

        {{-- Item --}}
        <a href="/artworks"
           class="group flex justify-between items-center border-b border-gray-200 pb-10">

            <div>

                <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-4">

                    Sculpture

                </p>

                <h3 class="museum-title text-3xl md:text-5xl font-light">

                    Silent Marble

                </h3>

                <p class="mt-4 text-gray-500 max-w-xl">

                    Witness timeless sculptures reflecting myth and legacy.

                </p>

            </div>

            <div class="flex items-center gap-4">

                <span
                    class="uppercase tracking-[0.2em] text-sm opacity-0 group-hover:opacity-100 transition duration-500">

                    Explore

                </span>

                <span
                    class="text-3xl transition duration-500 group-hover:translate-x-3">

                    →

                </span>

            </div>

        </a>

    </div>

</section>

@endsection