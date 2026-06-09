@extends('layouts.admin')

@section('title', 'Create Artwork')

@section('page-title', 'Create Artwork')

@section('breadcrumb', 'Artworks / Create')

@section('content')

    <div class="max-w-4xl">

        <div class="bg-white rounded-[32px] border border-gray-100 p-12">

            <form action="{{ route('admin.artworks.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-10">

                @csrf

                {{-- Title --}}
                <div>

                    <label class="block text-sm mb-3">

                        Artwork Title

                    </label>

                    <input type="text" name="title" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                </div>

                {{-- Artist --}}
                <div>

                    <label class="block text-sm mb-3">

                        Artist

                    </label>

                    <select name="artist_id" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                        @foreach ($artists as $artist)

                            <option value="{{ $artist->id }}">

                                {{ $artist->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Category --}}
                <div>

                    <label class="block text-sm mb-3">

                        Category

                    </label>

                    <select name="category_id" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                        @foreach ($categories as $category)

                            <option value="{{ $category->id }}">

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Museum --}}
                <div>

                    <label class="block text-sm mb-3">

                        Museum

                    </label>

                    <select name="museum_id" class="w-full rounded-2xl border border-gray-200 px-6 py-4">

                        @foreach ($museums as $museum)

                            <option value="{{ $museum->id }}">

                                {{ $museum->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Description --}}
                <div>

                    <label class="block text-sm mb-3">

                        Description

                    </label>

                    <textarea name="description" rows="6"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4"></textarea>

                </div>

                {{-- Image --}}
                <div>

                    <label class="block text-sm mb-3">

                        Artwork Image

                    </label>

                    <input type="file" name="image" class="w-full">

                </div>

                {{-- Submit --}}
                <button class="px-8 py-4 bg-black text-white rounded-2xl">

                    Create Artwork

                </button>

            </form>

        </div>

    </div>

@endsection