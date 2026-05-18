<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{


    public function index(Request $request)
    {
        $query = Ticket::with('exhibition');

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER TICKET TYPE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {

            $query->where('ticket_type', $request->type);

        }

        /*
        |--------------------------------------------------------------------------
        | SORT AVAILABLE FIRST
        |--------------------------------------------------------------------------
        */

        $query->orderByRaw("
        CASE
            WHEN status = 'Available' THEN 1
            WHEN status = 'Sold Out' THEN 2
            ELSE 3
        END
    ");

        $tickets = $query->latest()->get();

        return view('tickets.index', compact('tickets'));
    }





    public function show($id)
    {
        return redirect()->route('tickets.checkout', $id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exhibition_id' => 'required|exists:exhibitions,id',
            'type' => 'required|in:vip,regular,student',
            'price' => 'required|numeric',
            'available_quota' => 'required|integer',
        ]);

        $ticket = Ticket::create($validated);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'exhibition_id' => 'sometimes|exists:exhibitions,id',
            'type' => 'sometimes|in:vip,regular,student',
            'price' => 'sometimes|numeric',
            'available_quota' => 'sometimes|integer',
        ]);

        $ticket->update($validated);

        return response()->json([
            'message' => 'Ticket updated successfully',
            'data' => $ticket
        ]);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully'
        ]);
    }
}
