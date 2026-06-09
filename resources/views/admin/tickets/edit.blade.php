@extends('layouts.admin')

@section('title', 'Edit Ticket')
@section('page-title', 'Edit Ticket')
@section('breadcrumb', 'Tickets / Edit')

@section('content')

    <div class="max-w-4xl">
        <div class="bg-white rounded-[32px] border border-gray-100 p-12">
            <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Exhibition --}}
                <div>
                    <label class="block text-sm mb-3">Exhibition</label>
                    <select name="exhibition_id" class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                        @foreach ($exhibitions as $exhibition)
                            <option value="{{ $exhibition->id }}" {{ $ticket->exhibition_id == $exhibition->id ? 'selected' : '' }}>
                                {{ $exhibition->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ticket Type --}}
                <div>
                    <label class="block text-sm mb-3">Ticket Type</label>
                    <input type="text" name="ticket_type" value="{{ $ticket->ticket_type }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-sm mb-3">Price</label>
                    <input type="number" name="price" value="{{ $ticket->price }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Quota --}}
                <div>
                    <label class="block text-sm mb-3">Total Quota</label>
                    <input type="number" name="quota" value="{{ $ticket->quota }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Available Quota --}}
                <div>
                    <label class="block text-sm mb-3">Available Quota</label>
                    <input type="number" name="available_quota" value="{{ $ticket->available_quota }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Visit Date --}}
                <div>
                    <label class="block text-sm mb-3">Visit Date</label>
                    <input type="date" name="visit_date"
                        value="{{ \Carbon\Carbon::parse($ticket->visit_date)->format('Y-m-d') }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="px-8 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">
                    Update Ticket
                </button>
            </form>
        </div>
    </div>

@endsection