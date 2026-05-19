<header id="main-navbar" class="sticky top-0 z-50 w-full border-b border-white/5 bg-black transform transition-transform duration-500 ease-in-out">
    <nav class="text-white bg-black">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            
            {{-- Top Bar: Logo --}}
            <div class="flex items-center justify-between py-5 lg:py-6 relative">
                
                {{-- Left Side: Indikator Status Lokasi --}}
                <div class="hidden lg:flex items-center gap-2 bg-white/5 border border-white/10 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                    <span class="text-[10px] uppercase tracking-[0.2em] text-white/50 font-light">MEDAN, ID</span>
                </div>

                {{-- Center: Logo Utama Alphaseum --}}
                <a href="/" class="absolute left-1/2 transform -translate-x-1/2 group">
                    <h1 class="text-2xl lg:text-3xl font-serif tracking-[0.25em] font-light uppercase transition-all duration-500 group-hover:text-teal-400 group-hover:drop-shadow-[0_0_15px_rgba(45,212,191,0.5)]">
                        Alphaseum
                    </h1>
                </a>

                {{-- Right: Tickets Button Premium --}}
                <a href="/tickets"
                    class="relative inline-flex items-center gap-2 overflow-hidden bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-black px-6 py-2.5 rounded-full transition-all duration-300 text-xs font-semibold uppercase tracking-[0.15em] shadow-[0_4px_20px_rgba(45,212,191,0.2)] hover:shadow-[0_4px_25px_rgba(45,212,191,0.4)] hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span>Tickets</span>
                </a>
            </div>

            {{-- Bottom Bar: Main Navigation --}}
            <div class="flex items-center justify-center gap-12 py-4 border-t border-white/5">
                @php
                    $navItems = [
                        '/' => 'Visit',
                        '/exhibitions' => 'Exhibitions and Events',
                        '/artworks' => 'Explore'
                    ];
                @endphp

                @foreach($navItems as $url => $label)
                    <a href="{{ $url }}" class="relative text-xs uppercase tracking-[0.2em] font-medium transition duration-300 py-1 group
                       {{ ($url === '/' ? request()->is('/') : request()->is(trim($url, '/').'*')) ? 'text-white' : 'text-white/50 hover:text-white' }}">
                        {{ $label }}
                        <span class="absolute bottom-0 left-0 w-full h-[2px] bg-teal-400 scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100 {{ ($url === '/' ? request()->is('/') : request()->is(trim($url, '/').'*')) ? 'scale-x-100' : '' }}"></span>
                    </a>
                @endforeach

                {{-- Dropdown Menu --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] font-medium text-white/50 hover:text-white transition py-1">
                        See More
                        <svg class="w-3 h-3 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Content Premium --}}
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-4 w-56 bg-neutral-950 border border-white/10 rounded-xl shadow-[0_20px_50px_rgba(0,0,0,0.7)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0 overflow-hidden">
                        <div class="p-2 space-y-1">
                            @foreach(['/about' => 'About Us', '/collection' => 'Collection', '/visit' => 'Plan Your Visit', '/contact' => 'Contact'] as $link => $name)
                                <a href="{{ $link }}" class="block px-4 py-2.5 text-xs uppercase tracking-[0.1em] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition duration-200">
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </nav>

    {{-- Mobile Menu Toggle --}}
    <div class="lg:hidden bg-black border-t border-white/5">
        <button id="mobile-menu-toggle" class="w-full py-3.5 px-6 flex items-center justify-between text-white/70 hover:text-white transition">
            <span class="text-xs uppercase tracking-[0.2em] font-medium">Menu</span>
            <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Mobile Menu Content --}}
        <div id="mobile-menu" class="hidden bg-black border-t border-white/5">
            <div class="px-6 py-6 space-y-4">
                <a href="/" class="block text-sm uppercase tracking-[0.15em] {{ request()->is('/') ? 'text-teal-400 font-medium' : 'text-white/60' }}">Visit</a>
                <a href="/exhibitions" class="block text-sm uppercase tracking-[0.15em] {{ request()->is('exhibitions*') ? 'text-teal-400 font-medium' : 'text-white/60' }}">Exhibitions and Events</a>
                <a href="/artworks" class="block text-sm uppercase tracking-[0.15em] {{ request()->is('artworks*') ? 'text-teal-400 font-medium' : 'text-white/60' }}">Explore</a>
                
                <div class="pt-4 border-t border-white/5 space-y-3">
                    <a href="/about" class="block text-xs uppercase tracking-[0.1em] text-white/40 hover:text-white">About Us</a>
                    <a href="/collection" class="block text-xs uppercase tracking-[0.1em] text-white/40 hover:text-white">Collection</a>
                    <a href="/visit" class="block text-xs uppercase tracking-[0.1em] text-white/40 hover:text-white">Plan Your Visit</a>
                    <a href="/contact" class="block text-xs uppercase tracking-[0.1em] text-white/40 hover:text-white">Contact</a>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Logika JavaScript untuk Efek Swipe Out --}}
<script>
    // 1. Logika Toggle Menu Mobile
    document.getElementById('mobile-menu-toggle')?.addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        const icon = this.querySelector('svg');
        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });

    // 2. Logika Efek Swipe Out Navbar (500px)
    const navbar = document.getElementById('main-navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            // Jika scroll lebih dari 500px, lempar navbar ke luar layar (atas)
            navbar.classList.add('-translate-y-full');
        } else {
            // Jika kembali ke atas kurang dari 500px, kembalikan posisi navbar
            navbar.classList.remove('-translate-y-full');
        }
    });
</script>