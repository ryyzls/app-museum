@extends('layouts.app')

@section('title', 'Artworks')

@section('content')

    {{-- Hero Section --}}
    <section class="relative h-[70vh] overflow-hidden">

        <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=2070&auto=format&fit=crop"
            onerror="this.src='/images/fallback.jpg'" class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 h-full flex items-center">

            <div class="max-w-7xl mx-auto px-8 text-white">

                <p class="uppercase tracking-[0.4em] text-sm mb-6">
                    Museum Collection
                </p>

                <h1 class="museum-title text-6xl md:text-8xl font-light leading-none max-w-4xl">
                    Discover
                    Timeless Artworks
                </h1>

            </div>

        </div>

    </section>

    {{-- Artwork Collection --}}
    <section class="max-w-7xl mx-auto px-8 py-24">

        <div class="flex justify-between items-end mb-16">

            <div>

                <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-3">
                    Collection
                </p>

                <h2 class="museum-title text-5xl font-light">
                    Featured Artworks
                </h2>

            </div>

            <a href="/artworks/create"
                class="border border-black px-6 py-3 uppercase tracking-[0.2em] text-sm hover:bg-black hover:text-white transition duration-300">

                Add Artwork

            </a>

        </div>

        {{-- Search & Filter Section --}}
        <div class="mb-16 border-t border-b border-gray-200 py-8">

            <form method="GET" action="/artworks" class="space-y-6">

                {{-- Search Bar --}}
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by artwork title or artist name..."
                        class="w-full px-6 py-4 border border-gray-300 focus:border-black focus:outline-none uppercase tracking-[0.15em] text-sm">
                    @if(request('search'))
                        <a href="/artworks" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                            ✕
                        </a>
                    @endif
                </div>

                {{-- Filters Row --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Category Filter --}}
                    <select name="category"
                        class="px-4 py-3 border border-gray-300 focus:border-black focus:outline-none uppercase tracking-[0.15em] text-sm bg-white"
                        onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Artist Filter --}}
                    <select name="artist"
                        class="px-4 py-3 border border-gray-300 focus:border-black focus:outline-none uppercase tracking-[0.15em] text-sm bg-white"
                        onchange="this.form.submit()">
                        <option value="">All Artists</option>
                        @foreach($artists as $artist)
                            <option value="{{ $artist->id }}" {{ request('artist') == $artist->id ? 'selected' : '' }}>
                                {{ $artist->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Sort --}}
                    <select name="sort"
                        class="px-4 py-3 border border-gray-300 focus:border-black focus:outline-none uppercase tracking-[0.15em] text-sm bg-white"
                        onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A - Z</option>
                        <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z - A</option>
                    </select>

                    {{-- Apply Button --}}
                    <button type="submit"
                        class="border border-black px-6 py-3 uppercase tracking-[0.2em] text-sm hover:bg-black hover:text-white transition duration-300">
                        Apply Filters
                    </button>

                </div>

                {{-- Active Filters Display --}}
                @if(request()->hasAny(['search', 'category', 'artist', 'sort']))
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-gray-500 uppercase tracking-[0.2em]">Active:</span>

                        @if(request('search'))
                            <span class="px-3 py-1 bg-gray-100 border border-gray-300">
                                Search: "{{ request('search') }}"
                            </span>
                        @endif

                        @if(request('category'))
                            <span class="px-3 py-1 bg-gray-100 border border-gray-300">
                                {{ $categories->find(request('category'))->name }}
                            </span>
                        @endif

                        @if(request('artist'))
                            <span class="px-3 py-1 bg-gray-100 border border-gray-300">
                                {{ $artists->find(request('artist'))->name }}
                            </span>
                        @endif

                        <a href="/artworks" class="text-gray-500 hover:text-black uppercase tracking-[0.2em] ml-auto">
                            Clear All
                        </a>
                    </div>
                @endif

            </form>

        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

            @if($artworks->count())


                @foreach($artworks as $artwork)

                    <div class="group relative overflow-hidden transition-all duration-500 hover:-translate-y-2">

                        {{-- Image --}}
                        <div class="relative overflow-hidden bg-gray-200">

                            <img src="{{ $artwork->image_url }}" onerror="this.src='{{ asset('images/artworks/fallback.jpg') }}'"
                                class="h-[500px] w-full object-cover transition duration-500" alt="{{ $artwork->title }}">

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-70 transition duration-500">
                            </div>

                            <div
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.10),transparent_70%)]">
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="pt-6">

                            {{-- Category --}}
                            <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-3">

                                {{ $artwork->category->name }}

                            </p>

                            {{-- Title --}}
                            <h3 class="museum-title text-3xl font-light mb-3">

                                {{ $artwork->title }}

                            </h3>

                            {{-- Artist --}}
                            <p class="text-gray-700 mb-2">

                                by {{ $artwork->artist->name }}

                            </p>

                            {{-- Museum --}}
                            <p class="text-sm text-gray-500 mb-5">

                                {{ $artwork->museum->name }}

                            </p>

                            {{-- Description --}}
                            <p class="text-gray-600 leading-relaxed line-clamp-3">

                                {{ $artwork->description }}

                            </p>

                            {{-- Action --}}
                            <div class="mt-6">

                                <a href="/artworks/{{ $artwork->id }}"
                                    class="uppercase tracking-[0.2em] text-sm border-b border-black pb-1">

                                    View Details

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            @else

                <div class="col-span-full text-center py-32">

                    <h2 class="museum-title text-5xl font-light mb-6">
                        No Artworks Available
                    </h2>

                    <p class="text-gray-500 text-lg">
                        The collection is currently empty.
                    </p>

                </div>

            @endif

        </div>

        <div class="mt-20">
            {{ $artworks->links() }}
        </div>

    </section>

@endsection