<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'ticket'])->get();

        return response()->json($transactions);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user', 'ticket'])
            ->findOrFail($id);

        return response()->json($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        if ($ticket->stock < $validated['quantity']) {
            return response()->json([
                'message' => 'Ticket stock is not enough'
            ], 400);
        }

        $totalPrice = $ticket->price * $validated['quantity'];

        $transaction = Transaction::create([
            'user_id' => $validated['user_id'],
            'ticket_id' => $validated['ticket_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        $ticket->stock -= $validated['quantity'];
        $ticket->save();

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'data' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
