@extends('layouts.app')

@section('title', $artwork->title)

@section('content')

    {{-- Hero Section --}}
    <section class="relative h-screen overflow-hidden">

        {{-- Artwork Image --}}
        <img src="{{ $artwork->image_url }}" onerror="this.src='{{ asset('images/artworks/fallback.jpg') }}'"
            class="absolute inset-0 w-full h-full object-cover" alt="{{ $artwork->title }}">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/55"></div>

        {{-- Content --}}
        <div class="relative z-10 h-full flex items-end">

            <div class="max-w-7xl mx-auto px-8 pb-24 text-white w-full">

                {{-- Category --}}
                <p class="uppercase tracking-[0.4em] text-sm mb-6 text-gray-300">

                    {{ $artwork->category->name }}

                </p>

                {{-- Title --}}
                <h1 class="museum-title text-6xl md:text-8xl font-light leading-none max-w-5xl mb-8">

                    {{ $artwork->title }}

                </h1>

                {{-- Metadata --}}
                <div class="flex flex-col md:flex-row gap-8 text-sm uppercase tracking-[0.2em] text-gray-300">

                    <div>
                        Artist —
                        {{ $artwork->artist->name }}
                    </div>

                    <div>
                        Museum —
                        {{ $artwork->museum->name }}
                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- Detail Content --}}
    <section class="max-w-5xl mx-auto px-8 py-28">

        <div class="grid md:grid-cols-3 gap-16">

            {{-- Left Info --}}
            <div class="space-y-10">

                <div>

                    <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-3">
                        Artist
                    </p>

                    <h3 class="museum-title text-3xl font-light">
                        {{ $artwork->artist->name }}
                    </h3>

                </div>

                <div>

                    <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-3">
                        Category
                    </p>

                    <h3 class="museum-title text-3xl font-light">
                        {{ $artwork->category->name }}
                    </h3>

                </div>

                <div>

                    <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-3">
                        Museum
                    </p>

                    <h3 class="museum-title text-3xl font-light">
                        {{ $artwork->museum->name }}
                    </h3>

                </div>

            </div>

            {{-- Description --}}
            <div class="md:col-span-2">

                <p class="uppercase tracking-[0.3em] text-xs text-gray-500 mb-6">
                    Description
                </p>

                <div class="text-lg leading-[2.2rem] text-gray-700 space-y-6">

                    <p>
                        {{ $artwork->description }}
                    </p>

                </div>

                {{-- Action --}}
                <div class="mt-16 flex flex-wrap gap-6">

                    {{-- Back --}}
                    <a href="/artworks"
                        class="inline-flex items-center gap-3 uppercase tracking-[0.2em] text-sm border-b border-black pb-2 hover:gap-5 transition-all duration-300">

                        ← Back To Collection

                    </a>

                    {{-- Edit --}}
                    <a href="/artworks/{{ $artwork->id }}/edit"
                        class="uppercase tracking-[0.2em] text-sm border-b border-black pb-2">

                        Edit Artwork

                    </a>

                    {{-- Delete --}}
                    <form id="delete-form-{{ $artwork->id }}" action="{{ route('artworks.destroy', $artwork->id) }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="button" onclick="confirmDelete({{ $artwork->id }})"
                            class="uppercase tracking-[0.2em] text-sm border-b border-red-500 text-red-500 pb-2 hover:text-red-700 transition duration-300">

                            Delete Artwork

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </section>

@endsection

<script>

    function confirmDelete(id) {

        Swal.fire({

            title: 'Delete Artwork?',
            text: 'This action cannot be undone.',
            icon: 'warning',

            background: '#f8f6f2',
            color: '#111',

            showCancelButton: true,

            confirmButtonColor: '#111',
            cancelButtonColor: '#999',

            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',

            reverseButtons: true

        }).then((result) => {

            if (result.isConfirmed) {

                document.getElementById(`delete-form-${id}`).submit();

            }

        });

    }

</script>