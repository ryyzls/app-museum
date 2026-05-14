@extends('layouts.app')

@section('title', 'Visit The Museum')

@section('content')

<section class="max-w-7xl mx-auto px-8 py-28">

    {{-- Header --}}
    <div class="mb-24">

        <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-4">

            Museum Experience

        </p>

        <h1 class="museum-title text-6xl md:text-8xl font-light">

            Visit The Museum

        </h1>

        <p class="mt-8 max-w-2xl text-gray-500 leading-relaxed">

            Explore timeless collections, immersive exhibitions,
            and curated journeys through art and culture.

        </p>

    </div>

    {{-- Info Grid --}}
    <div class="grid md:grid-cols-2 gap-20 mb-32">

        {{-- Opening Hours --}}
        <div>

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-6">

                Opening Hours

            </p>

            <div class="space-y-5">

                <div class="flex justify-between border-b pb-4">

                    <span>Monday – Friday</span>
                    <span>09:00 – 18:00</span>

                </div>

                <div class="flex justify-between border-b pb-4">

                    <span>Saturday – Sunday</span>
                    <span>10:00 – 20:00</span>

                </div>

            </div>

        </div>

        {{-- Location --}}
        <div>

            <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-6">

                Location

            </p>

            <div class="space-y-4 text-gray-600">

                <p>National Museum</p>

                <p>
                    Jl. Museum Street No. 12<br>
                    Medan, Indonesia
                </p>

            </div>

        </div>

    </div>


    {{-- Admission --}}
    <div class="mb-32">

        <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-6">

            Admission

        </p>

        <h2 class="museum-title text-5xl font-light mb-16">

            Ticket Information

        </h2>

        <div class="space-y-10">

            {{-- Adult --}}
            <div class="flex justify-between border-b pb-6">

                <div>

                    <h3 class="museum-title text-3xl">

                        Adult

                    </h3>

                    <p class="text-gray-500 mt-2">

                        Full museum access

                    </p>

                </div>

                <span class="museum-title text-3xl">

                    Rp 50.000

                </span>

            </div>

            {{-- Student --}}
            <div class="flex justify-between border-b pb-6">

                <div>

                    <h3 class="museum-title text-3xl">

                        Student

                    </h3>

                    <p class="text-gray-500 mt-2">

                        Valid student identification required

                    </p>

                </div>

                <span class="museum-title text-3xl">

                    Rp 25.000

                </span>

            </div>

            {{-- Child --}}
            <div class="flex justify-between border-b pb-6">

                <div>

                    <h3 class="museum-title text-3xl">

                        Child

                    </h3>

                    <p class="text-gray-500 mt-2">

                        Under 12 years old

                    </p>

                </div>

                <span class="museum-title text-3xl">

                    Free

                </span>

            </div>

        </div>

    </div>

    {{-- CTA --}}
    <div class="text-center py-16 border-t">

        <h2 class="museum-title text-5xl mb-8">

            Plan Your Visit

        </h2>

        <button
            class="uppercase tracking-[0.3em] border border-black px-10 py-5 hover:bg-black hover:text-white transition duration-500">

            Reserve Visit

        </button>

    </div>

</section>

@endsection