@extends('layouts.app')

@section('title', 'Artworks Reviews - Alphaseum')

@section('content')
    <div class="bg-neutral-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">

        {{-- HEADER HALAMAN --}}
        <div class="max-w-7xl mx-auto text-center mb-12">
            <h1 class="text-4xl font-light tracking-[0.15em] uppercase font-serif text-neutral-900 mb-3">
                Artworks Reviews
            </h1>
            <p class="text-neutral-500 text-sm max-w-md mx-auto">
                Select one of the works below to leave a star rating and review.
            </p>
        </div>

        {{-- GRID UTAMA KARYA SENI --}}
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">

                @forelse($artworks as $artwork)
                    {{-- Setiap item memiliki scope Alpine sendiri untuk modalnya --}}
                    <div x-data="{ openModal: false }" class="h-full">

                        {{-- KARTU ARTWORK (DIKLIK UNTUK BUKA) --}}
                        <div @click="openModal = true"
                            class="group bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm hover:shadow-md transition duration-300 cursor-pointer flex flex-col h-full">

                            {{-- Kontainer Gambar Grid --}}
                            <div
                                class="w-full aspect-square bg-neutral-100 overflow-hidden relative border-b border-neutral-100">
                                <img src="{{ str_starts_with($artwork->image_url ?? '', 'http') ? $artwork->image_url : asset('storage/' . $artwork->image_url) }}" alt="{{ $artwork->title }}"
                                    class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?q=80&w=800';">
                            </div>

                            {{-- Informasi Singkat Grid --}}
                            <div class="p-4 flex flex-col flex-grow justify-between">
                                <h3
                                    class="font-serif text-sm text-neutral-800 tracking-wide line-clamp-2 uppercase leading-snug group-hover:text-[#008573] transition duration-200">
                                    {{ $artwork->title }}
                                </h3>
                                <p class="text-[11px] text-neutral-400 mt-2 tracking-wider uppercase">
                                    {{ Str::limit($artwork->artist->name ?? 'Unknown Artist', 20) }}
                                </p>
                            </div>
                        </div>

                        {{-- MODAL REVIEW POP-UP (MUNCUL DI TENGAH LAYAR) --}}
                        <div x-show="openModal" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                            style="display: none;">

                            {{-- Lapisan gelap luar untuk close --}}
                            <div class="absolute inset-0" @click="openModal = false"></div>

                            {{-- KOTAK MODAL TENGAH --}}
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto z-10 grid grid-cols-1 md:grid-cols-2"
                                @click.stop>

                                {{-- Tombol Close (X) --}}
                                <button @click="openModal = false"
                                    class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600 z-20 p-2 rounded-full hover:bg-neutral-100 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                {{-- SISI KIRI: Gambar Besar --}}
                                <div
                                    class="p-6 flex items-center justify-center bg-neutral-50 rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none border-b md:border-b-0 md:border-r border-neutral-100">
                                    <img src="{{ str_starts_with($artwork->image_url ?? '', 'http') ? $artwork->image_url : asset('storage/' . $artwork->image_url) }}" alt="{{ $artwork->title }}"
                                        class="max-h-[300px] md:max-h-[450px] w-auto object-contain rounded-lg shadow-md"
                                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?q=80&w=800';">
                                </div>

                                {{-- SISI KANAN: Detail Info & Form --}}
                                <div class="p-8 flex flex-col justify-between h-full">
                                    <div>
                                        <span
                                            class="text-[10px] bg-[#008573]/10 text-[#008573] font-semibold uppercase tracking-widest px-2.5 py-1 rounded-md">
                                            Artwork Detail
                                        </span>
                                        <h3 class="font-serif text-xl uppercase tracking-wider text-neutral-900 mt-4 mb-1">
                                            {{ $artwork->title }}</h3>
                                        <p class="text-xs text-neutral-400 uppercase tracking-widest mb-4">
                                            {{ $artwork->artist->name ?? 'Unknown Artist' }}</p>
                                        <p class="text-xs text-neutral-600 leading-relaxed max-h-[100px] overflow-y-auto pr-2">
                                            {{ $artwork->description ?? 'Tidak ada deskripsi untuk karya seni ini.' }}</p>
                                    </div>

                                    {{-- FORM PENILAIAN BINTANG --}}
                                    <div class="border-t border-neutral-100 pt-6 mt-6" x-data="{ rating: 0, hoverRating: 0 }">
                                        <span
                                            class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mb-2">PILIH
                                            RATING BINTANG:</span>

                                        <form action="/reviews/{{ $artwork->id }}" method="POST" class="space-y-4">
                                            @csrf

                                            <div class="flex items-center space-x-1 mb-2">
                                                <template x-for="star in 5">
                                                    <button type="button" @click="rating = star" @mouseover="hoverRating = star"
                                                        @mouseleave="hoverRating = 0"
                                                        class="p-0.5 transition duration-150 focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor" class="w-7 h-7 transition-colors"
                                                            :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-amber-400' : 'text-neutral-200'">
                                                            <path fill-rule="evenodd"
                                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>

                                            <input type="hidden" name="rating" :value="rating" required>

                                            <div>
                                                <label
                                                    class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mb-2">TULIS
                                                    KOMENTAR / ULASAN:</label>
                                                <textarea name="comment" rows="3" required
                                                    class="w-full text-sm rounded-xl border border-neutral-200 p-3 focus:outline-none focus:ring-2 focus:ring-[#008573]/20 focus:border-[#008573] placeholder-neutral-300 transition"
                                                    placeholder="Tulis ulasan impresi estetikamu terhadap artwork ini..."></textarea>
                                            </div>

                                            <button type="submit" :disabled="rating === 0"
                                                class="w-full h-11 bg-[#008573] text-white rounded-full font-semibold text-xs uppercase tracking-wider hover:bg-[#007465] disabled:opacity-40 disabled:cursor-not-allowed transition duration-300 shadow-sm">
                                                Kirim Penilaian
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-white rounded-xl border border-dashed border-neutral-300">
                        <p class="text-neutral-400 text-sm">Belum ada karya seni yang tersedia untuk diulas.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
@endsection