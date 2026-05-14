@extends('layouts.app')

@section('title', 'Exhibitions')

@section('content')

    <section class="max-w-7xl mx-auto px-8 py-24">

        <div class="mb-20">

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

                Current

            </p>

            <h1 class="museum-title text-6xl font-light">

                Exhibitions

            </h1>

        </div>

        <div class="space-y-16">

            @if($exhibitions->count())

                @foreach($exhibitions as $exhibition)

                    <div class="border-b border-gray-200 pb-12">

                        <div class="flex justify-between items-center">

                            <div>

                                <h2 class="museum-title text-4xl mb-4">

                                    {{ $exhibition->title }}

                                </h2>

                                <p class="text-gray-500 mb-2">

                                    {{ $exhibition->start_date->format('d M Y') }}
                                    —
                                    {{ $exhibition->end_date->format('d M Y') }}

                                </p>

                                <p class="text-gray-600">

                                    {{ $exhibition->museum->name }}

                                </p>

                                <p class="mt-4 text-sm uppercase tracking-[0.2em]">

                                    {{ $exhibition->artworks->count() }}
                                    artworks featured

                                </p>

                            </div>

                            <a href="/exhibitions/{{ $exhibition->id }}"
                                class="uppercase tracking-[0.2em] border-b border-black pb-2">

                                View Exhibition

                            </a>

                        </div>

                    </div>

                @endforeach

            @else

                <div class="text-center py-32">

                    <p class="uppercase tracking-[0.4em] text-sm text-gray-400 mb-5">

                        Exhibition Space

                    </p>

                    <h2 class="museum-title text-5xl font-light mb-6">

                        No Exhibitions Available

                    </h2>

                    <p class="text-gray-500 max-w-xl mx-auto">

                        Exhibition content will appear here once connected
                        to the museum ecosystem.

                    </p>

                </div>

            @endif

        </div>

    </section>

@endsection