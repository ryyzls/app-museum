<header class="relative z-50 bg-black">

    <nav class="bg-black text-white border-b border-white/10">

        <div class="max-w-7xl mx-auto px-6 lg:px-12">

            {{-- Top Bar: Logo + Utilities --}}
            <div class="flex items-center justify-between py-4 lg:py-5 border-b border-white/10">

                {{-- Left: Search --}}
                <button class="flex items-center gap-3 text-sm hover:text-white/80 transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="hidden lg:inline uppercase tracking-[0.15em] font-light">Search</span>
                </button>

                {{-- Center: Logo --}}
                <a href="/" class="absolute left-1/2 transform -translate-x-1/2">
                    <h1 class="text-2xl lg:text-4xl font-serif tracking-[0.15em] font-light uppercase">
                        Alphaseum
                    </h1>
                </a>

                {{-- Right: Tickets Button --}}
                <a href="/tickets"
                    class="flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white px-5 lg:px-6 py-2.5 rounded-full transition text-sm font-medium uppercase tracking-[0.1em]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span class="hidden lg:inline">Tickets</span>
                </a>

            </div>

            {{-- Bottom Bar: Main Navigation --}}
            <div class="flex items-center justify-center gap-8 lg:gap-16 py-5">

                <a href="/" class="text-sm lg:text-base uppercase tracking-[0.15em] font-medium transition duration-300 pb-1 border-b-2
                   {{ request()->is('/')
    ? 'border-white text-white'
    : 'border-transparent text-white/70 hover:text-white hover:border-white/40' }}">
                    Visit
                </a>

                <a href="/exhibitions" class="text-sm lg:text-base uppercase tracking-[0.15em] font-medium transition duration-300 pb-1 border-b-2
                   {{ request()->is('exhibitions*')
    ? 'border-white text-white'
    : 'border-transparent text-white/70 hover:text-white hover:border-white/40' }}">
                    Exhibitions and Events
                </a>

                <a href="/artworks" class="text-sm lg:text-base uppercase tracking-[0.15em] font-medium transition duration-300 pb-1 border-b-2
                   {{ request()->is('artworks*')
    ? 'border-white text-white'
    : 'border-transparent text-white/70 hover:text-white hover:border-white/40' }}">
                    Explore
                </a>

                {{-- Dropdown/More Menu --}}
                <div class="relative group">
                    <button
                        class="flex items-center gap-2 text-sm lg:text-base uppercase tracking-[0.15em] font-medium text-white/70 hover:text-white transition pb-1">
                        See More
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu (hidden by default, shown on hover) --}}
                    <div
                        class="absolute top-full right-0 mt-6 w-64 bg-black border border-white/20 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                        <div class="p-6 space-y-4">
                            <a href="/about"
                                class="block text-sm uppercase tracking-[0.1em] text-white/80 hover:text-white transition">
                                About Us
                            </a>
                            <a href="/collection"
                                class="block text-sm uppercase tracking-[0.1em] text-white/80 hover:text-white transition">
                                Collection
                            </a>
                            <a href="/visit"
                                class="block text-sm uppercase tracking-[0.1em] text-white/80 hover:text-white transition">
                                Plan Your Visit
                            </a>
                            <a href="/contact"
                                class="block text-sm uppercase tracking-[0.1em] text-white/80 hover:text-white transition">
                                Contact
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </nav>

    {{-- Mobile Menu Toggle (Hidden on desktop) --}}
    <div class="lg:hidden bg-black border-t border-white/10">
        <button id="mobile-menu-toggle" class="w-full py-4 px-6 flex items-center justify-between text-white">
            <span class="text-sm uppercase tracking-[0.15em] font-medium">Menu</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Mobile Menu Content --}}
        <div id="mobile-menu" class="hidden bg-black border-t border-white/10">
            <div class="px-6 py-6 space-y-6">

                <a href="/"
                    class="block text-base uppercase tracking-[0.15em] {{ request()->is('/') ? 'text-white font-semibold' : 'text-white/70' }}">
                    Visit
                </a>

                <a href="/exhibitions"
                    class="block text-base uppercase tracking-[0.15em] {{ request()->is('exhibitions*') ? 'text-white font-semibold' : 'text-white/70' }}">
                    Exhibitions and Events
                </a>

                <a href="/artworks"
                    class="block text-base uppercase tracking-[0.15em] {{ request()->is('artworks*') ? 'text-white font-semibold' : 'text-white/70' }}">
                    Explore
                </a>

                <div class="pt-4 border-t border-white/10 space-y-4">
                    <a href="/about" class="block text-sm uppercase tracking-[0.1em] text-white/60">
                        About Us
                    </a>
                    <a href="/collection" class="block text-sm uppercase tracking-[0.1em] text-white/60">
                        Collection
                    </a>
                    <a href="/visit" class="block text-sm uppercase tracking-[0.1em] text-white/60">
                        Plan Your Visit
                    </a>
                    <a href="/contact" class="block text-sm uppercase tracking-[0.1em] text-white/60">
                        Contact
                    </a>
                </div>

            </div>
        </div>
    </div>

</header>

{{-- Mobile Menu Script --}}
<script>
    // Toggle mobile menu
    document.getElementById('mobile-menu-toggle')?.addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        const icon = this.querySelector('svg');

        menu.classList.toggle('hidden');

        // Rotate icon
        if (menu.classList.contains('hidden')) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(180deg)';
        }
    });
</script>

<style>
    /* Ensure smooth transitions */
    header nav {
        transition: background-color 0.3s ease;
    }

    /* Dropdown animation */
    .group:hover>div {
        animation: fadeInDown 0.3s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile menu icon rotation */
    #mobile-menu-toggle svg {
        transition: transform 0.3s ease;
    }
</style>