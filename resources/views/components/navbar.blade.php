<header class="fixed top-0 left-0 w-full z-50">

    <nav class="bg-black/40 backdrop-blur-md text-white">

        <div class="max-w-7xl mx-auto px-8 py-5 flex items-center justify-between">

            {{-- Logo --}}
            <a href="/" class="museum-title text-3xl tracking-[0.25em] font-light uppercase">
                Alphaseum
            </a>

            {{-- Navigation --}}
            <div class="hidden md:flex items-center gap-10 text-sm uppercase tracking-[0.2em]">

                <a href="/artworks"
                    class="uppercase tracking-[0.3em] text-sm pb-2 border-b transition duration-300 {{ request()->is('artworks*') ? 'border-white text-white' : 'border-transparent text-white/80 hover:text-white hover:border-white/40' }}">

                    Artworks

                </a>

                <a href="/exhibitions"
                    class="uppercase tracking-[0.3em] text-sm pb-2 border-b transition duration-300 {{ request()->is('artworks*') ? 'border-white text-white' : 'border-transparent text-white/80 hover:text-white hover:border-white/40' }}">

                    Exhibitions

                </a>

                <a href="#" class="hover:text-gray-300 transition duration-300">
                    Tickets
                </a>

            </div>

        </div>

    </nav>

</header>