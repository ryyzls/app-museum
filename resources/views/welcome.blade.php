@extends('layouts.app')

@section('title', 'Welcome to Alphaseum')

@section('content')

    {{-- HERO SECTION --}}
    <section class="relative h-[430px] lg:h-[500px] overflow-hidden bg-black">

        {{-- Background Video --}}
        <video id="heroVideo"
            class="absolute inset-0 w-full h-full object-cover object-center  transition-opacity duration-700 ease-in-out opacity-100 will-change-transform"
            autoplay muted loop playsinline>

            <source src="/videos/museum-hero.mp4" type="video/mp4">

        </video>

        {{-- Fallback Image --}}
        <img id="heroFallback" src="https://api-www.louvre.fr/sites/default/files/2021-01/cour-napoleon-et-pyramide_1.jpg"
            alt="Museum Hero" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300 ease-in-out will-change-transform
                               opacity-0 pointer-events-none" />

        {{-- Pause / Play Button --}}
        <button id="videoToggle" class="absolute bottom-6 left-6 z-30
                                       w-11 h-11
                                       rounded-full
                                       bg-black/50 hover:bg-black/70
                                       backdrop-blur-md
                                       border border-white/10
                                       flex items-center justify-center
                                       transition duration-300 opacity-80 hover:opacity-100">

            {{-- Pause Icon --}}
            <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="currentColor"
                viewBox="0 0 24 24">

                <path d="M6 5h4v14H6zM14 5h4v14h-4z" />

            </svg>

            {{-- Play Icon --}}
            <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white hidden" fill="currentColor"
                viewBox="0 0 24 24">

                <path d="M8 5v14l11-7z" />

            </svg>

        </button>

        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/45"></div>

        {{-- Bottom Cinematic Gradient --}}
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

        {{-- HERO CARD --}}
        <div class="absolute bottom-0 left-0 right-0 z-20">

            <div class="max-w-[1550px] mx-auto px-6 lg:px-10">

                {{-- Louvre-style Horizontal Card --}}
                <div class="ml-auto w-full lg:w-[680px]">

                    <div class="bg-[#111111]/96 backdrop-blur-[2px] border border-white/10 shadow-2xl">

                        <div class="grid lg:grid-cols-12 min-h-[110px]">

                            {{-- LEFT CONTENT --}}
                            <div class="lg:col-span-7 px-8 py-5 flex flex-col justify-center border-r border-white/10">

                                {{-- Title --}}
                                <h1 class="text-white uppercase tracking-[0.14em]
                                                                                           text-[18px] md:text-[22px]
                                                                                           leading-[1.25] font-serif">

                                    Welcome to the <br class="hidden md:block">
                                    Alphaseum

                                </h1>

                                {{-- Subtitle --}}
                                <p class="mt-2 text-white/70 text-[13px] leading-relaxed">

                                    The museum is open today

                                </p>

                                {{-- Opening Hours --}}
                                <div class="mt-4 flex items-center gap-3 text-white font-serif">

                                    <span class="text-[18px] tracking-[0.08em]">
                                        9:00 AM
                                    </span>

                                    <span class="text-white/40 text-lg">
                                        →
                                    </span>

                                    <span class="text-[18px] tracking-[0.08em]">
                                        6:00 PM
                                    </span>

                                </div>

                            </div>

                            {{-- RIGHT BUTTONS --}}
                            <div class="lg:col-span-5 px-6 py-5 flex flex-col justify-center gap-3">

                                {{-- Button 1 --}}
                                <a href="/tickets" class="h-[52px]
                                                                                          rounded-full
                                                                                          bg-[#008573]
                                                                                          hover:bg-[#007465]
                                                                                          text-white
                                                                                          uppercase
                                                                                          tracking-[0.15em]
                                                                                          text-[11px]
                                                                                          font-semibold
                                                                                          flex items-center justify-center
                                                                                          transition duration-300">

                                    Book a ticket

                                </a>

                                {{-- Button 2 --}}
                                <a href="/visit" class="h-[52px]
                                                                                          rounded-full
                                                                                          bg-white
                                                                                          hover:bg-neutral-200
                                                                                          text-black
                                                                                          uppercase
                                                                                          tracking-[0.15em]
                                                                                          text-[11px]
                                                                                          font-semibold
                                                                                          flex items-center justify-center
                                                                                          transition duration-300">

                                    Prepare your visit

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- DATE HEADER SEPARATOR --}}
    <div class="bg-white border-b border-gray-200 px-12 py-6">

        <div class="w-full max-w-[90rem] mx-auto flex items-center gap-4">

            {{-- Museum Indicator --}}
            <img src="https://www.louvre.fr/assets/favicon/apple-touch-icon-76x76.png" alt="Museum Icon"
                class="w-4 h-4 object-contain opacity-80 mix-blend-multiply">

            {{-- Dynamic Date --}}
            <span class="text-xs font-serif tracking-[0.3em] uppercase text-black">

                {{ now()->format('l, d m Y') }}

            </span>

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


    <script>

        const heroVideo = document.getElementById('heroVideo');
        const heroFallback = document.getElementById('heroFallback');

        const videoToggle = document.getElementById('videoToggle');

        const pauseIcon = document.getElementById('pauseIcon');
        const playIcon = document.getElementById('playIcon');

        let isPaused = false;

        videoToggle.addEventListener('click', () => {

            if (!isPaused) {

                // Pause video
                heroVideo.pause();

                // Smooth fade transition
                heroVideo.classList.remove('opacity-100');
                heroVideo.classList.add('opacity-0');

                heroFallback.classList.remove('opacity-0');
                heroFallback.classList.add('opacity-100');

                // Toggle icons
                pauseIcon.classList.add('hidden');
                playIcon.classList.remove('hidden');

                isPaused = true;

            } else {

                // Play video
                heroVideo.play();

                // Smooth fade transition
                heroVideo.classList.remove('opacity-0');
                heroVideo.classList.add('opacity-100');

                heroFallback.classList.remove('opacity-100');
                heroFallback.classList.add('opacity-0');

                // Toggle icons
                pauseIcon.classList.remove('hidden');
                playIcon.classList.add('hidden');

                isPaused = false;

            }

        });

    </script>

@endsection