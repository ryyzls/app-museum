@extends('layouts.app')

@section('title', 'Create Artwork')

@section('content')

<section class="min-h-screen pt-40 pb-24 px-8">

    <div class="max-w-4xl mx-auto">

        {{-- Heading --}}
        <div class="mb-16">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">
                Artwork Management
            </p>

            <h1 class="museum-title text-6xl font-light">
                Create Artwork
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
        <form action="{{ route('artworks.store') }}"
              method="POST"
              class="space-y-10">

            @csrf

            {{-- Title --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
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
                >{{ old('description') }}</textarea>

            </div>

            {{-- Image --}}
            <div>

                <label class="block uppercase tracking-[0.2em] text-sm mb-4">
                    Image Path
                </label>

                <input
                    type="text"
                    name="image"
                    placeholder="/images/artworks/example.jpg"
                    value="{{ old('image') }}"
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

                    <option value="">Select Artist</option>

                    @foreach($artists as $artist)

                        <option value="{{ $artist->id }}">

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

                    <option value="">Select Category</option>

                    @foreach($categories as $category)

                        <option value="{{ $category->id }}">

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

                    <option value="">Select Museum</option>

                    @foreach($museums as $museum)

                        <option value="{{ $museum->id }}">

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

                    Create Artwork

                </button>

            </div>

        </form>

    </div>

</section>

@endsection