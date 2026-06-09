@extends('layouts.app')

@section('title', 'Welcome to Alphaseum')

@section('content')

    @php

        $filteredArtworks = $featuredArtworks->filter(function ($artwork) {

            return
                !empty($artwork->title) &&
                !empty($artwork->description) &&
                strlen(trim($artwork->description)) > 20 &&
                !empty($artwork->image_url);

        })->values();

    @endphp

    {{-- HERO SECTION --}}
    <section class="relative h-[430px] lg:h-[500px] overflow-hidden bg-black">

        {{-- Background Video --}}
        <video id="heroVideo"
            class="absolute inset-0 w-full h-full object-cover object-center transition-opacity duration-700 ease-in-out opacity-100 will-change-transform"
            autoplay muted loop playsinline>
            <source src="/videos/museum-hero.mp4" type="video/mp4">
        </video>

        {{-- Fallback Image --}}
        <img id="heroFallback" src="https://api-www.louvre.fr/sites/default/files/2021-01/cour-napoleon-et-pyramide_1.jpg"
            alt="Museum Hero"
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300 ease-in-out will-change-transform opacity-0 pointer-events-none" />

        {{-- Pause / Play Button --}}
        <button id="videoToggle"
            class="absolute bottom-6 left-6 z-30 w-11 h-11 rounded-full bg-black/50 hover:bg-black/70 backdrop-blur-md border border-white/10 flex items-center justify-center transition duration-300 opacity-80 hover:opacity-100">

            <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="currentColor"
                viewBox="0 0 24 24">
                <path d="M6 5h4v14H6zM14 5h4v14h-4z" />
            </svg>

            <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white hidden" fill="currentColor"
                viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>

        </button>

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/45"></div>

        {{-- Bottom Gradient --}}
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

        {{-- HERO CARD --}}
        <div class="absolute bottom-0 left-0 right-0 z-20">

            <div class="max-w-[1550px] mx-auto px-6 lg:px-10">

                <div class="ml-auto w-full lg:w-[680px]">

                    <div class="bg-[#111111]/96 backdrop-blur-[2px] border border-white/10 shadow-2xl">

                        <div class="grid lg:grid-cols-12 min-h-[110px]">

                            {{-- LEFT --}}
                            <div class="lg:col-span-7 px-8 py-5 flex flex-col justify-center border-r border-white/10">

                                <h1
                                    class="text-white uppercase tracking-[0.14em] text-[18px] md:text-[22px] leading-[1.25] font-serif">
                                    Welcome to
                                    <br class="hidden md:block">
                                    Alphaséum
                                </h1>

                                <p class="mt-2 text-white/70 text-[13px] leading-relaxed">
                                    Our museum is open today
                                </p>

                                <div class="mt-4 flex items-center gap-3 text-white font-serif">
                                    <span class="text-[18px] tracking-[0.08em]">9:00 AM</span>
                                    <span class="text-white/40 text-lg">→</span>
                                    <span class="text-[18px] tracking-[0.08em]">9:00 PM</span>
                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="lg:col-span-5 px-6 py-5 flex flex-col justify-center gap-3">

                                {{-- MAIN ACTION --}}
                                @auth

                                    @if(auth()->user()->role === 'admin')

                                        <a href="{{ route('admin.tickets.index') }}"
                                            class="h-[52px] rounded-full bg-[#008573] hover:bg-[#007465] text-white uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">

                                            Manage Tickets

                                        </a>

                                    @else

                                        <a href="/tickets"
                                            class="h-[52px] rounded-full bg-[#008573] hover:bg-[#007465] text-white uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">

                                            Buy a Ticket

                                        </a>

                                    @endif

                                @else

                                    <a href="/tickets"
                                        class="h-[52px] rounded-full bg-[#008573] hover:bg-[#007465] text-white uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">

                                        Book a Ticket

                                    </a>

                                @endauth


                                {{-- SECONDARY ACTION --}}
                                @auth

                                    <form method="POST" action="{{ route('logout') }}">

                                        @csrf

                                        <button type="submit"
                                            class="w-full h-[52px] rounded-full bg-white hover:bg-neutral-200 text-black uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">

                                            Logout

                                        </button>

                                    </form>

                                @else

                                    <a href="{{ route('login') }}"
                                        class="h-[52px] rounded-full bg-white hover:bg-neutral-200 text-black uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">

                                        Login

                                    </a>

                                @endauth

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- DATE BAR --}}
    <div class="bg-white px-6 md:px-12 py-10">

        <div class="w-full max-w-7xl mx-auto flex items-start gap-5">

            <div class="mt-1 shrink-0">

                <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="text-black">

                    <path d="M14 0L28 24H0L14 0Z" fill="currentColor" fill-opacity="0.1" />

                    <path d="M14 2L26 22.5H2L14 2Z" stroke="currentColor" stroke-width="1.5" />

                    <path d="M14 2V22.5" stroke="currentColor" stroke-width="1" />

                    <path d="M8 12H20" stroke="currentColor" stroke-width="1" />

                    <path d="M5 17H23" stroke="currentColor" stroke-width="1" />

                    <path d="M11 7H17" stroke="currentColor" stroke-width="1" />

                </svg>

            </div>

            <div class="flex flex-col gap-3">

                <h2 class="text-lg md:text-xl font-bold tracking-[0.15em] uppercase text-black font-sans">
                    {{ now()->format('l, d F Y') }}
                </h2>

                <div class="text-neutral-600 text-sm md:text-[15px] max-w-2xl leading-relaxed font-sans">
                    <p>Our museum is open, but some exhibition spaces may remain closed.</p>
                    <p>We apologize for any inconvenience.</p>
                </div>

            </div>

        </div>

    </div>


    {{-- HIGHLIGHTS SECTION --}}
    <section class="bg-white pt-10 pb-24">
        <div class="max-w-7xl mx-auto px-8">

            {{-- Section Header --}}
            <div class="mb-16">
                <h2
                    class="text-4xl md:text-5xl font-light tracking-[0.05em] font-serif border-b border-neutral-200 pb-6 uppercase">
                    Highlights
                </h2>
            </div>

            <div class="flex flex-col md:flex-row gap-10 items-start">

                {{-- LEFT COLUMN (KOLOM KIRI: 1 Pameran Utama Jan van Eyck + 2 Karya Seni Acak) --}}
                <div class="w-full md:w-2/3 flex flex-col">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

                        {{-- HARDCODED: JAN VAN EYCK (Pameran Utama) --}}
                        <a href="{{ url('/exhibitions/1') }}" class="md:col-span-2 group cursor-pointer flex flex-col">

                            <div class="relative overflow-hidden bg-neutral-100 w-full h-[300px] md:h-[520px] rounded-sm">
                                <div class="absolute top-4 left-4 z-10">
                                    <span
                                        class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                        Exhibition
                                    </span>
                                </div>
                                {{-- Menggunakan link gambar langsung dari Louvre --}}
                                <img src="https://api-www.louvre.fr/sites/default/files/styles/w1920_h823_c1/public/2024-02/jan-van-eyck-la-vierge-du-chancelier-rolin-vers-1430-huile-sur-bois-chene-paris-musee-du-louvre-inv-1271.jpg"
                                    alt="A New Look at Jan van Eyck"
                                    class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                            </div>

                            <div class="mt-6 mb-10">
                                <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                                    A New Look at Jan van Eyck
                                </h3>
                                <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                    An unprecedented exhibition structured around the recent conservation work on the
                                    Madonna of Chancellor Rolin, offering a deep dive into the mastery of the early
                                    Netherlandish painter.
                                </p>
                            </div>
                        </a>

                        {{-- FIRST ARTWORK (Tetap dinamis dari database untuk Artwork jika diperlukan) --}}
                        @if($featuredArtworks->has(0))
                            @php $artwork0 = $featuredArtworks->get(0); @endphp
                            <a href="{{ route('artworks.show', $artwork0->id) }}" class="group cursor-pointer flex flex-col">

                                <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                                    <div class="absolute top-4 left-4 z-10">
                                        <span
                                            class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                            Artwork
                                        </span>
                                    </div>
                                    <img src="{{ Str::startsWith($artwork0->image_url, ['http', 'https']) ? $artwork0->image_url : asset('storage/' . $artwork0->image_url) }}"
                                        alt="{{ $artwork0->title }}"
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-102"
                                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?q=80&w=800';">
                                </div>

                                <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                                    {{ $artwork0->title }}
                                </h3>
                                <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                    {{ Str::limit($artwork0->description, 120) }}
                                </p>
                            </a>
                        @endif

                        {{-- SECOND ARTWORK --}}
                        @if($featuredArtworks->has(1))
                            @php $artwork1 = $featuredArtworks->get(1); @endphp
                            <a href="{{ route('artworks.show', $artwork1->id) }}" class="group cursor-pointer flex flex-col">

                                <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                                    <div class="absolute top-4 left-4 z-10">
                                        <span
                                            class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                            Artwork
                                        </span>
                                    </div>
                                    <img src="{{ Str::startsWith($artwork1->image_url, ['http', 'https']) ? $artwork1->image_url : asset('storage/' . $artwork1->image_url) }}"
                                        alt="{{ $artwork1->title }}"
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-102"
                                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1579783928621-7a13d66a6211?q=80&w=800';">
                                </div>

                                <h3 class="font-serif text-xl tracking-wide uppercase text-neutral-900 mb-3 leading-snug">
                                    {{ $artwork1->title }}
                                </h3>
                                <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                    {{ Str::limit($artwork1->description, 150) }}
                                </p>
                            </a>
                        @endif

                    </div>
                </div>

                {{-- RIGHT COLUMN (KOLOM KANAN: Pameran Michelangelo Link + 1 Karya Seni Acak) --}}
                <div class="w-full md:w-1/3 flex flex-col gap-y-12">

                    {{-- HARDCODED: MICHELANGELO & RODIN (Mengarah langsung ke exhibitions/7) --}}
                    <a href="{{ url('/exhibitions/7') }}" class="group cursor-pointer flex flex-col">

                        <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                            <div class="absolute top-4 left-4 z-10">
                                <span
                                    class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                    Exhibition
                                </span>
                            </div>
                            {{-- Menggunakan link gambar langsung dari Louvre --}}
                            <img src="https://api-www.louvre.fr/sites/default/files/styles/w1000_h429_c1/public/2022-03/michel-ange-dit-michelangelo-buonarroti-esclave-rebelle-et-esclave-mourant-avant-restauration.jpg"
                                alt="Michelangelo & Rodin"
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                        </div>

                        <h3 class="font-serif text-xl tracking-widest uppercase text-neutral-900 mb-3">
                            Michelangelo & Rodin
                        </h3>
                        <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                            A historic creative dialogue between Michelangelo's iconic Renaissance sculptures and Auguste
                            Rodin's modern interpretations.
                        </p>
                    </a>

                    {{-- THIRD ARTWORK --}}
                    @if($filteredArtworks->values()->has(2))
                        @php
                            $artwork2 = $filteredArtworks->values()->get(2);
                        @endphp
                        <a href="{{ route('artworks.show', $artwork2->id) }}" class="group cursor-pointer flex flex-col pt-2">

                            <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                                <div class="absolute top-4 left-4 z-10">
                                    <span
                                        class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                        Artwork
                                    </span>
                                </div>
                                <img src="{{ Str::startsWith($artwork2->image_url, ['http', 'https']) ? $artwork2->image_url : asset('storage/' . $artwork2->image_url) }}"
                                    alt="{{ $artwork2->title }}"
                                    class="w-full h-full object-cover transition duration-500 group-hover:scale-102"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1547891654-e66ed7edd96c?q=80&w=800';">
                            </div>

                            <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                                {{ $artwork2->title }}
                            </h3>
                            <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                {{ Str::limit($artwork2->description, 120) }}
                            </p>
                        </a>
                    @endif

                </div>

            </div>
        </div>
    </section>


    {{-- CINEMATIC PARALLAX --}}
    <section id="louvre-parallax-section1" class="relative h-[420px] md:h-[560px] overflow-hidden bg-black">

        {{-- PARALLAX IMAGE --}}
        <div id="louvre-parallax-img1" class="absolute -top-[8%] left-0 w-full h-[120%] will-change-transform">
            <img src="https://api-www.louvre.fr/sites/default/files/2021-03/Louvre-Tuileries-Fleurissement-2019-139.jpg"
                alt="Alphaseum Garden" class="w-full h-full object-cover scale-[1.03]">

        </div>

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-black/25"></div>

    </section>

    {{-- MASONRY --}}
    <section class="bg-black pt-16 pb-20 text-white">
        <div class="max-w-[1600px] mx-auto px-4">

            {{-- Title --}}
            <div class="mb-8 pl-2">
                <h2 class="text-3xl md:text-4xl font-light tracking-[0.2em] uppercase font-serif">
                    EXPLORE DEEPER <br> AT THE ALPHASéUM
                </h2>
            </div>

            {{-- Container Grid dengan posisi Relative untuk menampung efek Fadeout --}}
            <div class="relative">

                {{-- True Masonry Dynamic Column Layout --}}
                <div class="columns-2 md:columns-3 gap-2 [column-fill:_balance]">


                    @foreach($filteredArtworks as $artwork)
                        <a href="{{ route('artworks.show', $artwork->id) }}"
                            class="mb-2 break-inside-avoid block w-full group relative overflow-hidden bg-neutral-900 transition duration-500">

                            <img src="{{ str_starts_with($artwork->image_url ?? '', 'http') ? $artwork->image_url : ($artwork->image_url ? asset('storage/' . $artwork->image_url) : asset('images/default-artwork.jpg')) }}"
                                alt="{{ $artwork->title }}"
                                class="w-full h-auto object-cover display-block transition duration-700 ease-out brightness-90 group-hover:brightness-100">

                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition duration-300">
                            </div>
                        </a>
                    @endforeach

                </div>

                {{-- Efek Fadeout Masking di Akhir Grid (Memudar ke Hitam) --}}
                <div
                    class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-black via-black/60 to-transparent pointer-events-none">
                </div>

            </div>

            {{-- Centered Explore Button --}}
            <div class="flex justify-center mt-12">
                <a href="/artworks"
                    class="bg-white text-black font-semibold text-xs uppercase tracking-[0.2em] px-10 py-3.5 rounded-full hover:bg-neutral-200 transition duration-300">
                    Explore
                </a>
            </div>

        </div>
    </section>


    {{-- CINEMATIC PARALLAX --}}
    <section id="louvre-parallax-section2" class="relative h-[420px] md:h-[560px] overflow-hidden bg-black">

        {{-- PARALLAX IMAGE --}}
        <div id="louvre-parallax-img2" class="absolute -top-[8%] left-0 w-full h-[120%] will-change-transform">

            <img src="https://api-www.louvre.fr/sites/default/files/2021-03/Louvre-Tuileries-Fleurissement-2019-139.jpg"
                alt="Parallax" class="w-full h-[120%] object-cover">

        </div>

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-black/20"></div>

    </section>


    {{-- CURATED EXPERIENCE --}}
    <section id="triggerSection" class="bg-[#f8f6f2] pt-32 pb-32">

        <div class="max-w-7xl mx-auto px-8">

            <div class="mb-20 border-l-4 border-black pl-6">

                <p class="uppercase tracking-[0.4em] text-xs text-gray-400 mb-5">
                    Curated Experience
                </p>

                <h2 class="museum-title text-5xl md:text-6xl font-light leading-none max-w-4xl">
                    Digital Gateway <br> to Eternal Art
                </h2>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">


                @foreach($filteredArtworks->take(3) as $artwork)

                    <div class="group">

                        <div class="overflow-hidden mb-6">

                            <img src="{{ str_starts_with($artwork->image_url ?? '', 'http') ? $artwork->image_url : ($artwork->image_url ? asset('storage/' . $artwork->image_url) : asset('images/default-artwork.jpg')) }}"
                                alt="{{ $artwork->title }}"
                                class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">

                        </div>

                        <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">
                            Collection
                        </p>

                        <h3 class="museum-title text-3xl font-light mb-4">
                            {{ $artwork->title }}
                        </h3>

                        <p class="text-gray-600 leading-relaxed">
                            {{ Str::limit($artwork->description, 120) }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>

    </section>


    {{-- INSTAGRAM SECTION --}}
    <section class="bg-[#121212] text-white py-20 relative overflow-hidden">

        <div class="max-w-7xl mx-auto px-8 relative">

            <div class="flex justify-between items-center mb-10 pl-2">

                <div>

                    <p class="uppercase tracking-[0.4em] text-xs text-gray-400 mb-3">
                        Latest News
                    </p>

                    <h2 class="text-4xl md:text-5xl font-light leading-none max-w-4xl text-white font-serif">
                        Explore the Latest <br> Alphaséum News
                    </h2>

                </div>

                <span class="text-xs text-gray-500 font-sans tracking-wide self-end mb-1">
                    Geser untuk melihat →
                </span>

            </div>


            <div class="flex overflow-x-auto space-x-6 pb-10 scrollbar-hide snap-x snap-mandatory px-2">

                @php

                    $filteredPosts = $instagramPosts->filter(function ($post) {

                        return
                            !empty($post->image_url) &&
                            !empty($post->description) &&
                            strlen(trim($post->description)) > 20;

                    });

                @endphp

                @foreach($filteredPosts as $post)

                    <div
                        class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">

                        <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">

                            <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">

                                <img src="{{ str_starts_with($post->image_url ?? '', 'http') ? $post->image_url : ($post->image_url ? asset('storage/' . $post->image_url) : asset('images/default-artwork.jpg')) }}"
                                    class="w-full h-full object-cover" alt="Avatar">

                            </div>

                            <div>

                                <p class="text-xs font-semibold text-white">
                                    alphaséum
                                </p>

                                <p class="text-[10px] text-gray-400">
                                    {{ $post->created_at->diffForHumans() }}
                                </p>

                            </div>

                        </div>

                        <div class="w-full h-[320px] bg-neutral-900">

                            <img src="{{ str_starts_with($post->image_url ?? '', 'http') ? $post->image_url : ($post->image_url ? asset('storage/' . $post->image_url) : asset('images/default-artwork.jpg')) }}"
                                class="w-full h-full object-cover" alt="{{ $post->title }}">

                        </div>

                        <div class="p-4">

                            <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">

                                <span class="font-bold text-white mr-1.5">
                                    alphaséum
                                </span>

                                {{ Str::limit($post->description, 120) }}

                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </section>

    {{-- FLOATING BAR --}}
    <div id="floatingBar"
        class="fixed bottom-0 left-0 right-0 z-50 w-full bg-[#111111] border-t border-white/10 shadow-2xl overflow-hidden pointer-events-none"
        style="opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease;">

        <div class="w-full px-16 py-4">

            <div class="flex items-center justify-between">

                <div class="flex items-center text-white">

                    <div class="pr-10 border-r border-white/20 shrink-0">

                        <h2 class="uppercase tracking-[0.18em] text-[13px] font-bold whitespace-nowrap">
                            Welcome to Alphaséum
                        </h2>

                    </div>

                    <div class="px-10 border-r border-white/20 shrink-0">

                        <p class="text-[13px] text-white/90 whitespace-nowrap">
                            We are Open Today!
                        </p>

                    </div>

                    <div class="px-10 shrink-0">

                        <div class="flex items-center gap-4">

                            <span class="text-[15px] font-semibold whitespace-nowrap">
                                9:00 AM
                            </span>

                            <span class="text-white/50">→</span>

                            <span class="text-[15px] font-semibold whitespace-nowrap">
                                9:00 PM
                            </span>

                        </div>

                    </div>

                </div>

                <div class="flex items-center gap-3 shrink-0">

                    {{-- MAIN ACTION --}}
                    @auth

                        @if(auth()->user()->role === 'admin')

                            <a href="{{ route('admin.tickets.index') }}"
                                class="h-[44px] px-7 rounded-full bg-[#008573] hover:bg-[#007465] text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">

                                Manage Tickets

                            </a>

                        @else

                            <a href="/tickets"
                                class="h-[44px] px-7 rounded-full bg-[#008573] hover:bg-[#007465] text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">

                                Buy a Ticket

                            </a>

                        @endif

                    @else

                        <a href="/tickets"
                            class="h-[44px] px-7 rounded-full bg-[#008573] hover:bg-[#007465] text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">

                            Book a Ticket

                        </a>

                    @endauth


                    {{-- SECONDARY ACTION --}}
                    @auth

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                class="h-[44px] px-7 rounded-full border border-white/30 hover:bg-white/10 text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">

                                Logout

                            </button>

                        </form>

                    @else

                        <a href="{{ route('login') }}"
                            class="h-[44px] px-7 rounded-full border border-white/30 hover:bg-white/10 text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">

                            Login

                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </div>


    {{-- SCRIPT --}}
    <script>
        const heroVideo = document.getElementById('heroVideo');
        const heroFallback = document.getElementById('heroFallback');
        const videoToggle = document.getElementById('videoToggle');
        const pauseIcon = document.getElementById('pauseIcon');
        const playIcon = document.getElementById('playIcon');

        let isPaused = false;

        videoToggle.addEventListener('click', () => {

            if (!isPaused) {

                heroVideo.pause();

                heroVideo.classList.remove('opacity-100');
                heroVideo.classList.add('opacity-0');

                heroFallback.classList.remove('opacity-0');
                heroFallback.classList.add('opacity-100');

                pauseIcon.classList.add('hidden');
                playIcon.classList.remove('hidden');

                isPaused = true;

            } else {

                heroVideo.play();

                heroVideo.classList.remove('opacity-0');
                heroVideo.classList.add('opacity-100');

                heroFallback.classList.remove('opacity-100');
                heroFallback.classList.add('opacity-0');

                pauseIcon.classList.remove('hidden');
                playIcon.classList.add('hidden');

                isPaused = false;

            }

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const floatingBar = document.getElementById('floatingBar');

            window.addEventListener('scroll', () => {

                if (window.scrollY > 500) {

                    floatingBar.style.opacity = '1';
                    floatingBar.style.transform = 'translateY(0)';
                    floatingBar.style.pointerEvents = 'auto';

                } else {

                    floatingBar.style.opacity = '0';
                    floatingBar.style.transform = 'translateY(40px)';
                    floatingBar.style.pointerEvents = 'none';

                }

            });

        });
    </script>
    <script>

        const parallaxSection1 = document.getElementById('louvre-parallax-section1');
        const parallaxImage1 = document.getElementById('louvre-parallax-img1');

        let currentY = 0;
        let targetY = 0;

        function animateParallax() {

            if (!parallaxSection1 || !parallaxImage1) return;

            const rect = parallaxSection1.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            if (rect.top < windowHeight && rect.bottom > 0) {

                const progress =
                    (windowHeight - rect.top) /
                    (windowHeight + rect.height);

                // SUBTLE MOVEMENT
                targetY = progress * -60;

            }

            // SMOOTH INTERPOLATION
            currentY += (targetY - currentY) * 0.08;

            parallaxImage1.style.transform =
                `translate3d(0, ${currentY}px, 0)`;

            requestAnimationFrame(animateParallax);

        }

        animateParallax();

    </script>

    <script>

        const parallaxSection2 = document.getElementById('louvre-parallax-section2');
        const parallaxImage2 = document.getElementById('louvre-parallax-img2');

        let currentY2 = 0;
        let targetY2 = 0;

        function animateParallax2() {

            if (!parallaxSection2 || !parallaxImage2) return;

            const rect = parallaxSection2.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            if (rect.top < windowHeight && rect.bottom > 0) {

                const progress =
                    (windowHeight - rect.top) /
                    (windowHeight + rect.height);

                targetY2 = progress * -60;

            }

            currentY2 += (targetY2 - currentY2) * 0.08;

            parallaxImage2.style.transform =
                `translate3d(0, ${currentY2}px, 0)`;

            requestAnimationFrame(animateParallax2);

        }

        animateParallax2();

    </script>

@endsection