<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use App\Models\Ticket;
use Carbon\Carbon;
class TicketSeeder extends Seeder
{ /** * Run the database seeds. */
    public function run(): void
    {
        $exhibitions = Exhibition::all();
        foreach ($exhibitions as $exhibition) { /* |-------------------------------------------------------------------------- | GENERATE VISIT DATE |-------------------------------------------------------------------------- */
            $startDate = Carbon::parse($exhibition->start_date); /* |-------------------------------------------------------------------------- | TICKET TYPES |-------------------------------------------------------------------------- */
            $tickets = [['ticket_type' => 'Regular', 'price' => 25.00, 'quota' => 300,], ['ticket_type' => 'Student', 'price' => 15.00, 'quota' => 150,], ['ticket_type' => 'VIP', 'price' => 60.00, 'quota' => 50,],];
            foreach ($tickets as $ticket) { /* |-------------------------------------------------------------------------- | AUTO STATUS |-------------------------------------------------------------------------- */

                $today = Carbon::today();

                $exhibitionEnd = Carbon::parse($exhibition->end_date);

                if ($today->gt($exhibitionEnd)) {

                    $status = 'Closed';

                } elseif ($ticket['quota'] <= 0) {

                    $status = 'Sold Out';

                } else {

                    $status = 'Available';
                }
                /* |-------------------------------------------------------------------------- | CREATE TICKET |-------------------------------------------------------------------------- */
                Ticket::create(['exhibition_id' => $exhibition->id, 'ticket_type' => $ticket['ticket_type'], 'price' => $ticket['price'], 'quota' => $ticket['quota'], 'available_quota' => $ticket['quota'], 'visit_date' => $startDate, 'status' => $status,]);
            }
        }
    }
}