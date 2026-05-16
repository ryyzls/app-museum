<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function checkout(Ticket $ticket)
    {
        $ticket->load('exhibition');

        return view('transactions.checkout', compact('ticket'));
    }


    public function reserve(Request $request, Ticket $ticket)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . $ticket->available_quota,
            ],

            'payment_method' => [
                'required',
                'string',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | TOTAL PRICE
        |--------------------------------------------------------------------------
        */

        $totalPrice = $ticket->price * $validated['quantity'];

        /*
        |--------------------------------------------------------------------------
        | GENERATE TRANSACTION CODE
        |--------------------------------------------------------------------------
        */

        $transactionCode =
            'ALP-' .
            strtoupper(uniqid());

        /*
        |--------------------------------------------------------------------------
        | CREATE TRANSACTION
        |--------------------------------------------------------------------------
        */

        $transaction = Transaction::create([

            'ticket_id' => $ticket->id,

            'quantity' => $validated['quantity'],

            'total_price' => $totalPrice,

            'payment_method' => $validated['payment_method'],

            'payment_status' => 'Pending',

            'transaction_code' => $transactionCode,

        ]);

        /*
        |--------------------------------------------------------------------------
        | REDUCE QUOTA
        |--------------------------------------------------------------------------
        */

        $ticket->available_quota -= $validated['quantity'];

        /*
        |--------------------------------------------------------------------------
        | AUTO STATUS
        |--------------------------------------------------------------------------
        */

        if ($ticket->available_quota <= 0) {

            $ticket->status = 'Sold Out';

        }

        $ticket->save();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('transactions.success', $transaction->id)
            ->with('success', 'Your reservation has been successfully created.');
    }


    public function success(Transaction $transaction)
    {
        $transaction->load('ticket.exhibition');

        return view('transactions.success', compact('transaction'));
    }


    public function downloadTicket(Transaction $transaction)
    {
        $transaction->load('ticket.exhibition');

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR
        |--------------------------------------------------------------------------
        */

        $qrCode = base64_encode(

            QrCode::format('svg')
                ->size(200)
                ->generate($transaction->transaction_code)

        );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'transactions.ticket-pdf',
            compact('transaction', 'qrCode')
        );

        return $pdf->download(
            'ALPHASEUM-TICKET-' .
            $transaction->transaction_code .
            '.pdf'
        );
    }









    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        if ($ticket->available_quota < $validated['quantity']) {
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
