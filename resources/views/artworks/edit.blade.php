@extends('layouts.app')

@section('title', 'Edit Artwork')

@section('content')

<section class="min-h-screen pt-40 pb-24 px-8">

    <div class="max-w-4xl mx-auto">

        {{-- Heading --}}
        <div class="mb-16">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">
                Artwork Management
            </p>

            <h1 class="museum-title text-6xl font-light">
                Edit Artwork
            </h1>

        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())

            <div class="mb-10 bg-red-100 border border-red-300 text-red-700 px-6 py-4">

                <ul class="space-y-2">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        {{-- Form --}}
        <form action="{{ route('artworks.update', $artwork->id) }}"
              method="POST"
              class="space-y-10">

            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $artwork->title) }}"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >

            </div>

            {{-- Description --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="6"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >{{ old('description', $artwork->description) }}</textarea>

            </div>

            {{-- Image --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Image Path
                </label>

                <input
                    type="text"
                    name="image"
                    value="{{ old('image', $artwork->image) }}"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >

            </div>

            {{-- Artist --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Artist
                </label>

                <select
                    name="artist_id"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >

                    @foreach($artists as $artist)

                        <option
                            value="{{ $artist->id }}"
                            {{ $artwork->artist_id == $artist->id ? 'selected' : '' }}
                        >

                            {{ $artist->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- Category --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Category
                </label>

                <select
                    name="category_id"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            {{ $artwork->category_id == $category->id ? 'selected' : '' }}
                        >

                            {{ $category->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- Museum --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Museum
                </label>

                <select
                    name="museum_id"
                    class="w-full border border-gray-300 bg-white px-6 py-4 focus:outline-none focus:border-black"
                >

                    @foreach($museums as $museum)

                        <option
                            value="{{ $museum->id }}"
                            {{ $artwork->museum_id == $museum->id ? 'selected' : '' }}
                        >

                            {{ $museum->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- Button --}}
            <div class="pt-6">

                <button
                    type="submit"
                    class="bg-black text-white px-10 py-4 uppercase tracking-[0.2em] text-sm hover:bg-gray-800 transition duration-300"
                >

                    Update Artwork

                </button>

            </div>

        </form>

    </div>

</section>

@endsection