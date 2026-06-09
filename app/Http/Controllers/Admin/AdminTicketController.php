<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Exhibition;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    /**
     * Tampilkan daftar tiket.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('exhibition');


        // SEARCH EXHIBITION
        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas('exhibition', function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%");

            });

        }

        // 1. FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. FILTER TICKET TYPE
        if ($request->filled('type')) {
            $query->where('ticket_type', $request->type);
        }

        // 3. SORT AVAILABLE FIRST
        $query->orderByRaw("
            CASE 
                WHEN status = 'Available' THEN 1
                WHEN status = 'Sold Out' THEN 2
                ELSE 3 
            END
        ")->latest();

        // Pakai paginate agar fungsi links() di blade tetap berjalan
        $tickets = $query->paginate(10)->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Tampilkan form pembuatan tiket baru.
     */
    public function create()
    {
        $exhibitions = Exhibition::latest()->get();
        return view('admin.tickets.create', compact('exhibitions'));
    }

    /**
     * Simpan data tiket ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exhibition_id' => 'required|exists:exhibitions,id',
            'ticket_type' => 'required|in:VIP,Regular,Student',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'visit_date' => 'required|date',
        ]);

        $validated['available_quota'] = $validated['quota'];
        $validated['status'] = 'Available';

        Ticket::create($validated);

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Redirect langsung ke halaman checkout (opsional).
     */
    public function show(Ticket $ticket)
    {
        return redirect()->route('tickets.checkout', $ticket->id);
    }

    /**
     * Tampilkan form edit tiket.
     */
    public function edit(Ticket $ticket)
    {
        $exhibitions = Exhibition::latest()->get();
        return view('admin.tickets.edit', compact('ticket', 'exhibitions'));
    }

    /**
     * Update data tiket di database.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'exhibition_id' => 'required|exists:exhibitions,id',
            'ticket_type' => 'required',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'available_quota' => 'required|integer|min:0',
            'visit_date' => 'required|date',
            ]);

        $ticket->update($validated);

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Hapus tiket dari database.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}