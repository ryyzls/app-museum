<footer class="relative mt-32 bg-black text-white overflow-hidden border-t border-white/5">
    
    {{-- High-End Ambient Background (Grid + Moving Glow) --}}
    <div class="absolute inset-0 bg-gradient-to-b from-neutral-950 via-black to-neutral-950 opacity-100"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff02_1px,transparent_1px),linear-gradient(to_bottom,#ffffff02_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    
    {{-- Decorative Premium Blur Lights --}}
    <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-teal-500/5 rounded-full blur-[140px] pointer-events-none animate-pulse" style="animation-duration: 8s;"></div>
    <div class="absolute -bottom-48 -left-48 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 pt-20 pb-12">

        {{-- 1. TOP SECTION: Branding & Modern Quote Box --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 pb-16 border-b border-white/5 items-center">
            
            {{-- Branding Text --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-3 py-1 rounded-full backdrop-blur-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-ping"></span>
                    <p class="uppercase tracking-[0.3em] text-[9px] text-teal-400 font-semibold">Digital Museum Experience</p>
                </div>
                <h2 class="text-3xl md:text-4xl font-serif tracking-[0.15em] font-light text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-neutral-500">
                    ALPHASEUM
                </h2>
                <p class="text-white/40 text-sm font-light max-w-sm leading-relaxed">
                    Sebuah platform museum digital pilihan yang menyajikan karya seni abadi, pameran interaktif, dan pengalaman budaya arsitektur secara global.
                </p>
            </div>

            {{-- Premium Quote Box (Dengan Border & Estetika Modern) --}}
            <div class="lg:col-span-7">
                <div class="relative max-w-md lg:ml-auto p-8 bg-gradient-to-br from-neutral-900/40 to-neutral-950/40 backdrop-blur-md border border-white/10 rounded-2xl group hover:border-teal-500/20 transition-all duration-500 shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                    
                    {{-- Simbol Kutipan Estetis Sisi Kiri Atas --}}
                    <span class="absolute top-4 left-4 text-4xl font-serif text-white/5 select-none leading-none group-hover:text-teal-400/10 transition-colors duration-500">“</span>
                    
                    <div class="relative space-y-4 pl-4 border-l-2 border-white/10 group-hover:border-teal-500/30 transition-colors duration-500">
                        <p class="text-base md:text-lg font-serif font-light leading-relaxed text-white/80 group-hover:text-white transition-colors duration-300 italic">
                            “Art enables us to find ourselves and lose ourselves at the same time.”
                        </p>
                        
                        <div class="flex items-center gap-2 pt-1">
                            <span class="w-4 h-px bg-white/20 group-hover:bg-teal-500/40 transition-colors duration-500"></span>
                            <p class="text-[11px] uppercase tracking-[0.2em] font-mono text-white/40 group-hover:text-teal-400/80 transition-colors duration-300">
                                Thomas Merton
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MIDDLE SECTION: Navigation Links & Upcoming Events --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 py-16 border-b border-white/5">
            
            {{-- Column 1: Explore --}}
            <div class="space-y-4">
                <p class="uppercase tracking-[0.25em] text-xs font-semibold text-white/30">Explore</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/artworks" class="text-white/50 hover:text-white transition-colors duration-200 font-light flex items-center gap-1 group">Artworks Online <span class="opacity-0 translate-x-[-5px] group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 text-teal-400">→</span></a></li>
                    <li><a href="/exhibitions" class="text-white/50 hover:text-white transition-colors duration-200 font-light flex items-center gap-1 group">Pameran Virtual <span class="opacity-0 translate-x-[-5px] group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 text-teal-400">→</span></a></li>
                    <li><a href="/tickets" class="text-white/50 hover:text-white transition-colors duration-200 font-light flex items-center gap-1 group">Booking Tiket <span class="opacity-0 translate-x-[-5px] group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 text-teal-400">→</span></a></li>
                </ul>
            </div>

            {{-- Column 2: Platform Info --}}
            <div class="space-y-4">
                <p class="uppercase tracking-[0.25em] text-xs font-semibold text-white/30">Platform</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/about" class="text-white/50 hover:text-white transition-colors duration-200 font-light">Filosofi Kami</a></li>
                    <li><a href="/collection" class="text-white/50 hover:text-white transition-colors duration-200 font-light">Pengarsipan Digital</a></li>
                    <li><a href="/contact" class="text-white/50 hover:text-white transition-colors duration-200 font-light">Partnerships</a></li>
                </ul>
            </div>

            {{-- Column 3: Museum Hours (Live Status) --}}
            <div class="space-y-4">
                <p class="uppercase tracking-[0.25em] text-xs font-semibold text-white/30">Jadwal Museum</p>
                <div class="space-y-1 text-sm text-white/50 font-light">
                    <p class="text-white/80 font-normal">Sen — Min</p>
                    <p>09:00 — 20:00 WIB</p>
                    <div class="inline-flex items-center gap-1.5 pt-2 text-xs text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)]"></span>
                        <span>Terbuka untuk umum</span>
                    </div>
                </div>
            </div>

            {{-- Column 4: Upcoming Events --}}
            <div class="space-y-4">
                <p class="uppercase tracking-[0.25em] text-xs font-semibold text-white/30">Upcoming Events</p>
                <ul class="space-y-3 text-sm font-light text-white/60">
                    <li class="flex items-center gap-2.5 group cursor-pointer">
                        <span class="w-1 h-1 rounded-full bg-teal-400 transition-transform duration-300 group-hover:scale-150"></span>
                        <span class="group-hover:text-white transition-colors">Digital Renaissance</span>
                    </li>
                    <li class="flex items-center gap-2.5 group cursor-pointer">
                        <span class="w-1 h-1 rounded-full bg-teal-400 transition-transform duration-300 group-hover:scale-150"></span>
                        <span class="group-hover:text-white transition-colors">Ancient Egypt Week</span>
                    </li>
                    <li class="flex items-center gap-2.5 group cursor-pointer">
                        <span class="w-1 h-1 rounded-full bg-teal-400 transition-transform duration-300 group-hover:scale-150"></span>
                        <span class="group-hover:text-white transition-colors">Virtual Gallery Tour</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- 3. BOTTOM SECTION: Legal, Tech Stack & Back to Top --}}
        <div class="flex flex-col sm:flex-row justify-between items-center pt-8 gap-6">
            
            {{-- Copyright --}}
            <div class="text-center sm:text-left order-3 sm:order-1">
                <p class="text-white/20 text-[11px] tracking-widest uppercase font-mono">
                    © 2026 ALPHASEUM. DESIGNED FOR CULTURAL ELEGANCE.
                </p>
            </div>

            {{-- Subtle Tech Stack Pills --}}
            <div class="flex items-center gap-3 text-[10px] font-mono text-white/20 order-2 bg-white/[0.01] border border-white/5 px-4 py-1.5 rounded-full backdrop-blur-sm">
                <span class="hover:text-white/50 transition-colors">Laravel</span>
                <span class="text-white/5">•</span>
                <span class="hover:text-white/50 transition-colors">Tailwind</span>
                <span class="text-white/5">•</span>
                <span class="hover:text-white/50 transition-colors">MySQL</span>
            </div>

            {{-- Premium Back to Top Button --}}
            <div class="order-1 sm:order-3">
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="group flex items-center gap-2 text-xs uppercase tracking-widest text-white/40 hover:text-teal-400 transition-colors duration-300 bg-transparent border border-white/5 hover:border-teal-500/30 px-4 py-2 rounded-full backdrop-blur-sm">
                    <span>Top</span>
                    <svg class="w-3 h-3 transform group-hover:-translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7 7 7M12 3v18" />
                    </svg>
                </button>
            </div>

        </div>

    </div>
</footer>