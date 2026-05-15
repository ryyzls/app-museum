@extends('layouts.app')

@section('title', 'Welcome to Alphaseum')

@section('content')

    {{-- HERO SECTION --}}
    {{-- h-[calc(100vh-88px)] → penuh dari bawah navbar (88px) sampai bawah viewport, persis seperti Louvre --}}
    <section class="relative h-[calc(100vh-88px)] min-h-[600px] overflow-hidden bg-black font-sans z-10">

        {{-- BACKGROUND VIDEO --}}
        <video
            class="absolute inset-0 w-full h-full object-cover"
            aria-hidden="true" tabindex="-1" preload="auto" autoplay loop muted playsinline>
            <source type="video/mp4" src="/videos/museum-hero.mp4">
        </video>

        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20 z-0"></div>

        {{-- Hero Content --}}
        <div class="relative z-10 h-full flex items-center">
            <div class="w-full max-w-[90rem] mx-auto px-12">

                <div class="grid lg:grid-cols-12 gap-8 items-center bg-black/55 backdrop-blur-xl border border-white/10 p-10 max-w-5xl rounded-sm shadow-2xl mx-auto lg:mx-0">

                    {{-- Left: Text --}}
                    <div class="lg:col-span-7 text-white lg:border-r lg:border-white/10 lg:pr-8">
                        <h1 class="font-serif text-3xl md:text-4xl uppercase tracking-[0.15em] leading-tight text-white/95">
                            Welcome to the <br>Alphaseum
                        </h1>

                        <p class="mt-4 text-white/70 text-xs md:text-sm tracking-wide leading-relaxed max-w-md">
                            The museum is open today. Discover 4,500+ authentic masterpieces, immersive exhibitions, and
                            curated artistic heritage.
                        </p>

                        <div class="mt-6 flex items-center gap-2 text-xl md:text-2xl font-serif tracking-widest text-white/90">
                            <span>9:00 AM</span>
                            <span class="text-white/40 mx-2">→</span>
                            <span>9:00 PM</span>
                        </div>
                    </div>

                    {{-- Right: Buttons --}}
                    <div class="lg:col-span-5 flex flex-col gap-4 w-full lg:pl-4">

                        <a href="/tickets"
                            class="flex items-center justify-center gap-3 bg-[#008573] hover:bg-[#007061] text-white font-medium py-4 px-8 rounded-full tracking-[0.2em] text-[11px] uppercase transition-all duration-300 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                            Book a ticket
                        </a>

                        <a href="/artworks"
                            class="flex items-center justify-center gap-3 bg-white hover:bg-neutral-200 text-black font-medium py-4 px-8 rounded-full tracking-[0.2em] text-[11px] uppercase transition-all duration-300 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m11.25 11.25.041-.02a.75.75 0 1 1 .512 1.352l-.041.02-.01.006a.75.75 0 0 1-.512-1.352l.01-.006ZM12 2.25c4.28 0 7.75 3.47 7.75 7.75a7.75 7.75 0 0 1-7.75 7.75A7.75 7.75 0 0 1 4.25 10c0-4.28 3.47-7.75 7.75-7.75Z" />
                            </svg>
                            Prepare your visit
                        </a>

                    </div>

                </div>
            </div>
        </div>

    </section>

    {{-- DATE HEADER SEPARATOR --}}
    <div class="bg-white border-b border-gray-200 px-12 py-6">
        <div class="w-full max-w-[90rem] mx-auto flex items-center gap-4 text-xs font-serif tracking-[0.3em] uppercase text-black">
            <svg class="w-4 h-4 text-black animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
            </svg>
            Saturday, 16 May 2026
        </div>
    </div>

    {{-- FEATURED SECTION --}}
    <section class="bg-[#f8f6f2] py-32">
        <div class="max-w-7xl mx-auto px-8">

            {{-- Heading --}}
            <div class="mb-20 border-l-4 border-black pl-6">
                <p class="uppercase tracking-[0.4em] text-xs text-gray-400 mb-5">Curated Experience</p>
                <h2 class="museum-title text-5xl md:text-6xl font-light leading-none max-w-4xl">
                    A Digital Gateway <br>to Timeless Art
                </h2>
            </div>

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                {{-- Card 1 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=1200&auto=format&fit=crop"
                            alt="Explore Artworks"
                            class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Collection</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Explore Artworks</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Browse curated masterpieces from classical antiquities to modern artistic expressions.
                    </p>
                </div>

                {{-- Card 2 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1501612780327-45045538702b?q=80&w=1200&auto=format&fit=crop"
                            alt="Immersive Exhibitions"
                            class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Exhibition</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Immersive Exhibitions</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Experience thematic exhibitions designed to connect visitors with artistic stories.
                    </p>
                </div>

                {{-- Card 3 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1518998053901-5348d3961a04?q=80&w=1200&auto=format&fit=crop"
                            alt="Reserve Your Visit"
                            class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Visit</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Reserve Your Visit</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Plan your museum journey and discover curated cultural experiences through our ticketing ecosystem.
                    </p>
                </div>

            </div>
        </div>
    </section>

@endsection