<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('exhibition')->get();

        return response()->json($tickets);
    }

    public function show($id)
    {
        $ticket = Ticket::with('exhibition')->findOrFail($id);

        return response()->json($ticket);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exhibition_id' => 'required|exists:exhibitions,id',
            'type' => 'required|in:vip,regular,student',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
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
            'stock' => 'sometimes|integer',
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
