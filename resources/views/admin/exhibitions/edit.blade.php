@extends('layouts.admin')

@section('title', 'Create Exhibition')

@section('page-title', 'Create Exhibition')

@section('breadcrumb', 'Exhibitions / Create')

@section('content')

    <div class="max-w-5xl">

        <div
            class="bg-white rounded-[32px] border border-gray-100
                                                                                                        p-12 shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

            <form action="{{ route('admin.exhibitions.update', $exhibition) }}" method="POST" enctype="multipart/form-data"
                class="space-y-10">

                @csrf
                @method('PUT')

                {{-- Title --}}
                <div>

                    <label class="block text-sm mb-3">

                        Exhibition Title

                    </label>

                    <input type="text" name="title" value="{{ $exhibition->title }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                </div>

                {{-- Subtitle --}}
                <div>

                    <label class="block text-sm mb-3">

                        Subtitle

                    </label>

                    <input type="text" name="subtitle" value="{{ $exhibition->subtitle }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                </div>

                {{-- Museum --}}
                <div>

                    <label class="block text-sm mb-3">

                        Museum

                    </label>

                    <select name="museum_id" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                        @foreach ($museums as $museum)

                            <option value="{{ $museum->id }}" {{ $exhibition->museum_id == $museum->id ? 'selected' : '' }}>

                                {{ $museum->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Status --}}
                <div>

                    <label class="block text-sm mb-3">

                        Status

                    </label>


                    <select name="status" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                        <option value="Upcoming" {{ $exhibition->status == 'Upcoming' ? 'selected' : '' }}>

                            Upcoming

                        </option>

                        <option value="Ongoing" {{ $exhibition->status == 'Ongoing' ? 'selected' : '' }}>

                            Ongoing

                        </option>

                        <option value="Past" {{ $exhibition->status == 'Past' ? 'selected' : '' }}>

                            Past

                        </option>

                    </select>



                </div>

                {{-- Dates --}} 
                <div class="grid grid-cols-2 gap-8"> 
                    {{-- Start Date --}} 
                    <div> <label
                            class="block text-sm mb-3"> Start Date </label> 
                            <input type="date" name="start_date"
                            value="{{ \Carbon\Carbon::parse($exhibition->start_date)->format('Y-m-d') }}"
                            class="w-full rounded-2xl border border-gray-200 px-6 py-4"></div> 
                    {{-- End Date --}} 
                    <div>
                        <label class="block text-sm mb-3"> End Date </label> <input type="date" name="end_date"
                            value="{{ \Carbon\Carbon::parse($exhibition->end_date)->format('Y-m-d') }}"
                            class="w-full rounded-2xl border border-gray-200 px-6 py-4"></div>
                </div>

                {{-- Description --}}
                <div>

                    <label class="block text-sm mb-3">

                        Description

                    </label>

                    <textarea name="description" rows="7"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">{{ $exhibition->description }}</textarea>

                </div>

                {{-- Banner --}}
                <div>

                    <label class="block text-sm mb-3">

                        Exhibition Banner

                    </label>

                    <input type="file" name="banner" class="w-full">

                </div>

                {{-- Artworks --}}
                <div>

                    <label class="block text-sm mb-5">

                        Attach Artworks

                    </label>

                    {{-- Search + Filter --}}
                    <div class="flex gap-4 mb-6">

                        {{-- Search --}}
                        <input type="text" id="artworkSearch" placeholder="Search artworks..."
                            class="flex-1 rounded-2xl border border-gray-200 px-6 py-4">

                        {{-- Category Filter --}}
                        <select id="categoryFilter" class="rounded-2xl border border-gray-200 px-6 py-4">

                            <option value="all">

                                All Categories

                            </option>

                            @foreach ($categories as $category)

                                <option value="{{ $category->id }}">

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Artwork List --}}
                    <div
                        class="grid grid-cols-2 gap-4 max-h-[400px]
                                                                                                                    overflow-y-auto border border-gray-200
                                                                                                                    rounded-3xl p-6">

                        @foreach ($artworks as $artwork)

                                        <label data-title="{{ strtolower($artwork->title) }}" data-category="{{ $artwork->category_id }}"
                                            class="flex items-center gap-4 p-4 rounded-2xl
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            hover:bg-black/5 transition duration-200 cursor-pointer">

                                            <input type="checkbox" name="artworks[]" value="{{ $artwork->id }}" {{ $exhibition->artworks->contains($artwork->id) ? 'checked' : '' }} class="w-5 h-5 rounded">

                                            <img src="{{ str_starts_with($artwork->image_url, 'http')
                            ? $artwork->image_url
                            : asset('storage/' . $artwork->image_url) }}" class="w-16 h-16 rounded-2xl object-cover">

                                            <div>

                                                <p class="text-sm font-medium">

                                                    {{ $artwork->title }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $artwork->artist->name ?? '-' }}

                                                </p>

                                            </div>

                                        </label>

                        @endforeach

                    </div>

                </div>

                {{-- Submit --}}
                <button class="px-8 py-4 bg-black text-white rounded-2xl">

                    Update Exhibition

                </button>

            </form>

        </div>

    </div>

    <script>

        const searchInput = document.getElementById('artworkSearch');

        const categoryFilter = document.getElementById('categoryFilter');

        const artworkItems = document.querySelectorAll('[data-title]');

        function filterArtworks() {

            const search = searchInput.value.toLowerCase();

            const category = categoryFilter.value;

            artworkItems.forEach(item => {

                const title = item.dataset.title;

                const artworkCategory = item.dataset.category;

                const matchesSearch = title.includes(search);

                const matchesCategory =
                    category === 'all' ||
                    artworkCategory === category;

                if (matchesSearch && matchesCategory) {

                    item.style.display = 'flex';

                } else {

                    item.style.display = 'none';

                }

            });

        }

        searchInput.addEventListener('input', filterArtworks);

        categoryFilter.addEventListener('change', filterArtworks);

    </script>

@endsection