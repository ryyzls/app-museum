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
                                    Selamat Datang di <br class="hidden md:block">
                                    Alphaseum
                                </h1>
                                {{-- Subtitle --}}
                                <p class="mt-2 text-white/70 text-[13px] leading-relaxed">
                                    Museum kami buka hari ini
                                </p>
                                {{-- Opening Hours --}}
                                <div class="mt-4 flex items-center gap-3 text-white font-serif">
                                    <span class="text-[18px] tracking-[0.08em]">9:00 AM</span>
                                    <span class="text-white/40 text-lg">→</span>
                                    <span class="text-[18px] tracking-[0.08em]">9:00 PM</span>
                                </div>
                            </div>
                            {{-- RIGHT BUTTONS --}}
                            <div class="lg:col-span-5 px-6 py-5 flex flex-col justify-center gap-3">
                                {{-- Button 1 --}}
                                <a href="/tickets" class="h-[52px] rounded-full bg-[#008573] hover:bg-[#007465] text-white uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">
                                    Pesan Tiket
                                </a>
                                {{-- Button 2 --}}
                                <a href="/register" class="h-[52px] rounded-full bg-white hover:bg-neutral-200 text-black uppercase tracking-[0.15em] text-[11px] font-semibold flex items-center justify-center transition duration-300">
                                    Daftar Akun
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- LOUVRE-STYLE DATE BAR --}}
    <div class="bg-white px-6 md:px-12 py-10">
        <div class="w-full max-w-7xl mx-auto flex items-start gap-5">
            {{-- Pyramid Icon SVG --}}
            <div class="mt-1 shrink-0">
                <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-black">
                    <path d="M14 0L28 24H0L14 0Z" fill="currentColor" fill-opacity="0.1"/>
                    <path d="M14 2L26 22.5H2L14 2Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M14 2V22.5" stroke="currentColor" stroke-width="1"/>
                    <path d="M8 12H20" stroke="currentColor" stroke-width="1"/>
                    <path d="M5 17H23" stroke="currentColor" stroke-width="1"/>
                    <path d="M11 7H17" stroke="currentColor" stroke-width="1"/>
                </svg>
            </div>
            {{-- Date & Info Content --}}
            <div class="flex flex-col gap-3">
                <h2 class="text-lg md:text-xl font-bold tracking-[0.15em] uppercase text-black font-sans">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </h2>
                <div class="text-neutral-600 text-sm md:text-[15px] max-w-2xl leading-relaxed font-sans">
                    <p>Museum kami buka dan beberapa ruang pameran mungkin tetap ditutup.</p>
                    <p>Kami mohon maaf atas ketidaknyamanan ini.</p>
                </div>
            </div>
        </div>
    </div>


    {{-- 1. HIGHLIGHTS SECTION --}}
    <section class="bg-white pt-10 pb-24">
        <div class="max-w-7xl mx-auto px-8">
            <div class="mb-16">
                <h2 class="text-4xl md:text-5xl font-light tracking-[0.05em] font-serif border-b border-neutral-200 pb-6 uppercase">
                    Highlights
                </h2>
            </div>

            <div class="flex flex-col md:flex-row gap-10 items-start">
                <div class="w-full md:w-2/3 flex flex-col">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                        
                        <div class="md:col-span-2 group cursor-pointer flex flex-col">
                            <div class="relative overflow-hidden bg-neutral-100 w-full h-[300px] md:h-[520px] rounded-sm">
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                        Exhibition
                                    </span>
                                </div>
                                <img src="{{ asset('images/rodin.jpg') }}" alt="Michelangelo Rodin" class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                            </div>
                            <div class="mt-6 mb-10">
                                <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                                    MICHELANGELO RODIN
                                </h3>
                                <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                    Michelangelo dan Rodin, dengan menjadikan tubuh sebagai subjek utama karya seni mereka, menunjukkan bahwa keduanya memandang tubuh sebagai sesuatu yang dijiwai oleh kehidupan batin yang intens.
                                </p>
                            </div>
                        </div>

                        <div class="group cursor-pointer flex flex-col">
                            <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                        Visitor Trail
                                    </span>
                                </div>
                                <img src="{{ asset('images/monalisa.jpg') }}" alt="Alphaseum Masterpieces" class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                            </div>
                            <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                                ALPHASEUM MASTERPIECES
                            </h3>
                            <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                Karya seni yang paling disukai dalam sejarah Alphaseum.
                            </p>
                        </div>

                        <div class="group cursor-pointer flex flex-col">
                            <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                        Restoration
                                    </span>
                                </div>
                                <img src="{{ asset('images/vandyck.jpg') }}" alt="Restoration" class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                            </div>
                            <h3 class="font-serif text-xl tracking-wide uppercase text-neutral-900 mb-3 leading-snug">
                                ‘Lukisan ini tidak kehilangan kompleksitasnya semakin lama Anda melihatnya - justru semakin kaya’
                            </h3>
                            <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                                Potret Raja Charles I dari Inggris, karya Anthony Van Dyck, kembali menghiasi dinding galeri setelah lebih dari setahun menjalani perawatan konservasi. Blaise Ducos, Kurator Lukisan Flemish dan Belanda, membahas mahakarya ini.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="w-full md:w-1/3 flex flex-col gap-y-12">
                    <div class="group cursor-pointer flex flex-col">
                        <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                            <div class="absolute top-4 left-4 z-10">
                                <span class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                    Exhibition
                                </span>
                            </div>
                            <img src="{{ asset('images/martin.webp') }}" alt="Martin Schongauer" class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                        </div>
                        <h3 class="font-serif text-xl tracking-widest uppercase text-neutral-900 mb-3">
                            MARTIN SCHONGAUER
                        </h3>
                        <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                            Martin Schongauer adalah salah satu seniman Jermanik paling populer di akhir Abad Pertengahan – dan salah satu tokoh utama pada periode ini.
                        </p>
                    </div>

                    <div class="group cursor-pointer flex flex-col pt-2">
                        <div class="relative overflow-hidden bg-neutral-100 aspect-square w-full mb-6 rounded-sm">
                            <div class="absolute top-4 left-4 z-10">
                                <span class="bg-[#2a4e63]/90 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 font-medium rounded-sm">
                                    Family
                                </span>
                            </div>
                            <img src="{{ asset('images/mulai.webp') }}" alt="Mulai Perjalanan" class="w-full h-full object-cover transition duration-500 group-hover:scale-102">
                        </div>
                        <h3 class="font-serif text-2xl tracking-widest uppercase text-neutral-900 mb-3">
                            MULAI PERJALANAN
                        </h3>
                        <p class="text-neutral-900 text-sm font-semibold leading-relaxed">
                            Beberapa karya lainnya yang disukai di alphaseum
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- RUANG KOSONG PERTAMA (Setelah Highlights & Sebelum Section Gambar) --}}
    <div class="w-full h-[500px] bg-transparent bg-fixed bg-cover bg-center" 
         style="background-image: url('{{ asset('images/background.jpg') }}');">
    </div>


    {{-- 2. MASONRY GALLERY — Black Background --}}
    <section class="bg-black pt-20 pb-28 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-8 relative">
        
        <h2 class="text-3xl md:text-4xl font-serif tracking-widest uppercase mb-12 border-b border-gray-800 pb-4 text-white">
            Menggali lebih dalam <br> di alphaseum
        </h2>
        
        <div class="relative">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Left Column --}}
                <div class="space-y-8">
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1578321272176-b7bbc0679853?q=80&w=1400&auto=format&fit=crop" class="w-full h-[500px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 1">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1200&auto=format&fit=crop" class="w-full h-[320px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 2">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group h-[200px]">
                        <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="Artwork 3">
                    </div>
                </div>

                {{-- Middle Column --}}
                <div class="space-y-8">
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=1200&auto=format&fit=crop" class="w-full h-[320px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 4">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1577720643272-265f09367456?q=80&w=1200&auto=format&fit=crop" class="w-full h-[500px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 5">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group h-[200px]">
                        <img src="https://images.unsplash.com/photo-1573152958734-1922c188fba3?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="Artwork 6">
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-8">
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=1000&auto=format&fit=crop" class="w-full h-[500px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 7">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group">
                        <img src="https://images.unsplash.com/photo-1578301978018-3005759f48f7?q=80&w=1000&auto=format&fit=crop" class="w-full h-[320px] object-cover transition duration-700 group-hover:scale-105" alt="Artwork 8">
                    </div>
                    <div class="overflow-hidden bg-neutral-900 group h-[200px]">
                        <img src="https://images.unsplash.com/photo-1580136579312-94651dfd596d?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="Artwork 9">
                    </div>
                </div>

            </div>

            <div class="absolute inset-x-0 bottom-0 h-[160px] bg-gradient-to-t from-black via-black/90 to-transparent pointer-events-none"></div>

            <div class="absolute bottom-2 inset-x-0 flex justify-center z-10">
                <a href="/artworks" class="bg-white text-black font-semibold px-12 py-3.5 rounded-full shadow-2xl hover:bg-neutral-200 transition duration-300 transform hover:scale-105 tracking-widest text-sm uppercase">
                    Explore
                </a>
            </div>

        </div>
    </div>
</section>


    {{-- RUANG KOSONG KEDUA (Setelah Section Gambar & Sebelum Curated Experience) --}}
    <div class="w-full h-[500px] bg-transparent bg-fixed bg-cover bg-center" 
         style="background-image: url('{{ asset('images/background.jpg') }}');">
    </div>


    {{-- 3. FEATURED SECTION (Curated Experience - Pindah Paling Bawah dengan Jarak Atas Ideal) --}}
    <section id="triggerSection" class="bg-[#f8f6f2] pt-32 pb-32">
        <div class="max-w-7xl mx-auto px-8">

            {{-- Heading --}}
            <div class="mb-20 border-l-4 border-black pl-6">
                <p class="uppercase tracking-[0.4em] text-xs text-gray-400 mb-5">Curated Experience</p>
                <h2 class="museum-title text-5xl md:text-6xl font-light leading-none max-w-4xl">
                    Gerbang Digital <br>Menuju Seni Abadi
                </h2>
            </div>

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                {{-- Card 1 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=1200&auto=format&fit=crop" alt="Explore Artworks" class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Collection</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Jelajahi Karya Seni</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Jelajahi karya-karya pilihan dari zaman kuno klasik hingga ekspresi artistik modern.
                    </p>
                </div>

                {{-- Card 2 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1501612780327-45045538702b?q=80&w=1200&auto=format&fit=crop" alt="Immersive Exhibitions" class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Exhibition</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Pameran Imersif</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Alami pameran tematik yang dirancang untuk menghubungkan pengunjung dengan cerita seni.
                    </p>
                </div>

                {{-- Card 3 --}}
                <div class="group">
                    <div class="overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1518998053901-5348d3961a04?q=80&w=1200&auto=format&fit=crop" alt="Reserve Your Visit" class="h-[500px] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <p class="uppercase tracking-[0.35em] text-[11px] text-gray-400 mb-4">Visit</p>
                    <h3 class="museum-title text-3xl font-light mb-4">Pesan Kunjungan Anda</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Rencanakan perjalanan museum Anda dan temukan pengalaman budaya pilihan melalui ekosistem tiket kami.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#121212] text-white py-20 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-8 relative">
        
        <div class="flex justify-between items-center mb-10 pl-2">
            <div>
                <p class="uppercase tracking-[0.4em] text-xs text-gray-400 mb-3">Kabar Terbaru</p>
                <h2 class="text-4xl md:text-5xl font-light leading-none max-w-4xl text-white font-serif">
                    Jelajahi Kabar <br>Virtual Alphaseum
                </h2>
            </div>
            <span class="text-xs text-gray-500 font-sans tracking-wide self-end mb-1">Geser untuk melihat →</span>
        </div>

        <div class="flex overflow-x-auto space-x-6 pb-10 scrollbar-hide snap-x snap-mandatory px-2">
            
            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">8 JAM YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1578321272176-b7bbc0679853?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 1">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Kisah sakral dalam guratan klasik. Mengenang fajar keabadian🏛️✝️
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">1 HARI YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 2">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Bukan kita yang sedang memandangi seni, melainkan seni itu sendiri yang sedang menatap lurus ke dalam jiwa kita. Sebuah dialog tanpa kata di sudut galeri.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">5 HARI YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 3">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Seni abstrak di galeri modern: di mana seniman berhenti mendikte bentuk, dan membiarkan mata kita menemukan ceritanya sendiri di balik aliran warna.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">1 MINGGU YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 4">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Terpaku di depan salah satu sudut paling dramatis di museum. Permainan kontras cahaya yang membuat kelopak-kelopak ini seolah hidup dan memancarkan energinya sendiri menembus kegelapan.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">2 MINGGU YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1577720643272-265f09367456?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 5">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Keanggunan yang membeku dalam waktu. Langkahnya terhenti di ambang pintu, namun pesonanya tetap melangkah jauh menembus zaman hingga sampai di galeri ini.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">2 MINGGU YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1578301978018-3005759f48f7?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 6">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Menemukan pelarian kecil di balik sapuan warna klasik. Damai yang tak lekang oleh waktu.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">3 MINGGU YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 7">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Di mana kata-kata menemui jalannya yang buntu, di situlah warna-warna mulai berbicara. Menggoreskan rasa, menghidupkan kanvas.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">4 MINGGU YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1573152958734-1922c188fba3?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 8">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Ketika seni tidak lagi sekadar dinikmati dengan mata, melainkan dirasakan oleh raga. Manusia dan karya melebur dalam satu ruang yang sama.
                    </p>
                </div>
            </div>

            <div class="flex-none w-[320px] bg-[#1a1a1a] rounded-xl overflow-hidden shadow-2xl border border-neutral-800 snap-start">
                <div class="flex items-center p-4 space-x-3 border-b border-neutral-800">
                    <div class="w-9 h-9 rounded-full bg-neutral-700 overflow-hidden">
                        <img src="{{ asset('images/monalisa.jpg') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">alphaseum</p>
                        <p class="text-[10px] text-gray-400">1 BULAN YANG LALU</p>
                    </div>
                </div>
                <div class="w-full h-[320px] bg-neutral-900">
                    <img src="https://images.unsplash.com/photo-1580136579312-94651dfd596d?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Post 9">
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                        <span class="font-bold text-white mr-1.5">alphaseum</span>Penataan figur-figur yang saling bertubrukan menciptakan skala pertempuran yang terasa kolosal dan megah, mirip dengan lukisan-lukisan sejarah klasik Eropa abad ke-18 atau ke-19.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


    {{-- FLOATING BAR --}}
    <div id="floatingBar"
    class="fixed bottom-0 left-0 right-0 z-50 w-full bg-[#111111] border-t border-white/10 shadow-2xl overflow-hidden pointer-events-none"
    style="opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease;">
        <div class="w-full px-16 py-4">
            <div class="flex items-center justify-between">
                {{-- LEFT SIDE --}}
                <div class="flex items-center text-white">
                    {{-- TITLE --}}
                    <div class="pr-10 border-r border-white/20 shrink-0">
                        <h2 class="uppercase tracking-[0.18em] text-[13px] font-bold whitespace-nowrap">
                            Selamat Datang di Alphaseum
                        </h2>
                    </div>
                    {{-- OPEN TODAY --}}
                    <div class="px-10 border-r border-white/20 shrink-0">
                        <p class="text-[13px] text-white/90 whitespace-nowrap">
                            Museum kami buka hari ini
                        </p>
                    </div>
                    {{-- HOURS --}}
                    <div class="px-10 shrink-0">
                        <div class="flex items-center gap-4">
                            <span class="text-[15px] font-semibold whitespace-nowrap">9:00 WIB</span>
                            <span class="text-white/50">→</span>
                            <span class="text-[15px] font-semibold whitespace-nowrap">20.00 WIB</span>
                        </div>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center gap-3 shrink-0">
                    <a href="/tickets" class="h-[44px] px-7 rounded-full bg-[#008573] hover:bg-[#007465] text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Pesan Tiket
                    </a>
                    <a href="/register" class="h-[44px] px-7 rounded-full border border-white/30 hover:bg-white/10 text-white font-semibold text-[13px] flex items-center justify-center gap-2 whitespace-nowrap transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Daftar Akun
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- SCRIPTS --}}
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

@endsection