@extends('layouts.admin')

@section('title', 'Create Ticket')
@section('page-title', 'Create Ticket')
@section('breadcrumb', 'Tickets / Create')

@section('content')

    <div class="max-w-4xl">
        <div class="bg-white rounded-[32px] border border-gray-100 p-12">
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.tickets.store') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Exhibition --}}
                <div>
                    <label class="block text-sm mb-3">Exhibition</label>
                    <select name="exhibition_id" id="exhibition_select"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4" {{ $exhibitions->isEmpty() ? 'disabled' : '' }}>
                        @if($exhibitions->isEmpty())
                            <option value="">-- No active exhibitions available --</option>
                        @else
                            @foreach ($exhibitions as $exhibition)
                                <option value="{{ $exhibition->id }}"
                                    data-start="{{ \Carbon\Carbon::parse($exhibition->start_date)->format('Y-m-d') }}"
                                    data-end="{{ \Carbon\Carbon::parse($exhibition->end_date)->format('Y-m-d') }}">
                                    {{ $exhibition->title }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    @if($exhibitions->isEmpty())
                        <p class="text-red-500 text-sm mt-2">You need an active exhibition to create tickets.</p>
                    @endif
                </div>

                {{-- Ticket Type --}}
                <div>
                    <label class="block text-sm mb-3">Ticket Type</label>
                    <select name="ticket_type" class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                        <option value="Regular">Regular</option>
                        <option value="VIP">VIP</option>
                        <option value="Student">Student</option>
                    </select>
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-sm mb-3">Price</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Quota --}}
                <div>
                    <label class="block text-sm mb-3">Quota</label>
                    <input type="number" name="quota" value="{{ old('quota') }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Visit Date --}}
                <div>
                    <label class="block text-sm mb-3">Visit Date</label>
                    <input type="date" name="visit_date" id="visit_date" value="{{ old('visit_date') }}"
                        class="w-full rounded-2xl border border-gray-200 px-6 py-4">
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="px-8 py-4 bg-black text-white rounded-2xl hover:opacity-80 transition duration-300">
                    Create Ticket
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const exhibitionSelect = document.getElementById('exhibition_select');
            const visitDate = document.getElementById('visit_date');

            function updateDateLimits() {
                // Mencegah error jika belum ada pameran sama sekali
                if (exhibitionSelect.options.length === 0) return;

                const selected = exhibitionSelect.options[exhibitionSelect.selectedIndex];

                visitDate.min = selected.dataset.start;
                visitDate.max = selected.dataset.end;
            }

            // Jalankan setiap kali pilihan pameran diubah
            exhibitionSelect.addEventListener('change', updateDateLimits);

            // Jalankan sekali saat halaman pertama kali dimuat
            updateDateLimits();
        });
    </script>

@endsection