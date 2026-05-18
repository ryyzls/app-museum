@extends('layouts.admin')

@section('title', 'Manage Artworks')

@section('content')

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-14">

            <div>

                <p class="uppercase tracking-[0.3em] text-xs text-gray-400 mb-4">

                    Collection Management

                </p>

                <h1 class="museum-title text-6xl font-light">

                    Artworks

                </h1>

            </div>

            <a href="{{ route('admin.artworks.create') }}"
                class="px-8 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">

                Add Artwork

            </a>

        </div>

        {{-- Table --}}
        <div
            class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-[32px] overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.04)]">

            <table class="w-full">

                <thead class="border-b border-gray-100">

                    <tr class="text-left">

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">

                            Artwork

                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">

                            Artist

                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">

                            Category

                        </th>

                        <th class="px-8 py-6 text-xs uppercase tracking-[0.3em] text-gray-400">

                            Museum

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach ($artworks as $artwork)

                        <tr class="border-b border-gray-100 hover:bg-black/5 transition duration-200">

                            <td class="px-8 py-6">

                                <div class="flex items-center gap-5">

                                    <img src="{{ str_starts_with($artwork->image_url, 'http') ? $artwork->image_url : asset('storage/' . $artwork->image_url) }}"
                                        class="w-20 h-20 object-cover rounded-2xl">

                                    <div>

                                        <h2 class="text-lg">

                                            {{ $artwork->title }}

                                        </h2>


                                    </div>

                                </div>

                            </td>

                            <td class="px-8 py-6">

                                {{ $artwork->artist->name ?? '-' }}

                            </td>

                            <td class="px-8 py-6">

                                {{ $artwork->category->name ?? '-' }}

                            </td>

                            <td class="px-8 py-6">

                                {{ $artwork->museum->name ?? '-' }}

                            </td>

                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3"> <a href="{{ route('admin.artworks.edit', $artwork) }}"
                                        class="px-5 py-3 rounded-2xl border border-gray-200 hover:bg-black hover:text-white transition duration-300">
                                        Edit </a>
                                    <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST"
                                        onsubmit="return confirm('Delete this artwork?')"> @csrf @method('DELETE') <button
                                            class="px-5 py-3 rounded-2xl border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition duration-300">
                                            Delete </button> </form>
                                </div>
                            </td>



                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        <div class="mt-10">

            {{ $artworks->links() }}

        </div>

    </div>

@endsection