<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BIKIN ADMIN DAN USER PALSU
        // Bikin 1 Akun Admin buat kamu login besok
        User::factory()->create([
            'name' => 'Admin Museum',
            'email' => 'admin@museum.com',
            'role' => 'admin', // Sesuaikan dengan role admin kamu
            'password' => bcrypt('password'), // Passwordnya: password
        ]);

        User::factory()->create([

            'name' => 'Museum Visitor',

            'email' => 'user@museum.com',

            'role' => 'user',

            'password' => bcrypt('password'),

        ]);

        // Bikin 50 Akun pengunjung palsu
        User::factory(50)->create();

        // 2. JALANKAN SEEDER ASLI KAMU (Aman, pakai limit 150)
        $this->call([
            LouvreSeeder::class,
            ExhibitionSeeder::class,
            TicketSeeder::class
        ]);

        // 3. GENERATE 1000 TRANSAKSI PALSU UNTUK PAMER KE DOSEN
        $faker = Faker::create('id_ID');

        // Ambil data User dan Tiket yang barusan terbuat
        $userIds = User::where('role', '!=', 'admin')->pluck('id')->toArray();
        $tickets = Ticket::all()->keyBy('id');
        $ticketIds = $tickets->keys()->toArray();

        $transactions = [];

        // Kalau tabel tiketnya kosong (error safety), kita skip transaksinya
        if (!empty($ticketIds) && !empty($userIds)) {
            for ($i = 0; $i < 1000; $i++) {
                $randomTicketId = $faker->randomElement($ticketIds);
                $ticket = $tickets[$randomTicketId];

                $qty = $faker->randomElement([
                    1,
                    1,
                    1,
                    1,
                    2,
                    2,
                    2,
                    3,
                    3,
                    3,
                    4,
                    4
                ]);

                $totalPrice = $qty * $ticket->price;

                $transactions[] = [

                    'user_id' => $faker->randomElement($userIds),

                    'ticket_id' => $randomTicketId,

                    'transaction_code' =>
                        'TRX-' . $faker->date('Ymd') . '-' . strtoupper(Str::random(6)),

                    'quantity' => $qty,

                    'total_price' => $totalPrice,

                    'visit_date' =>
                        $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),

                    'payment_status' =>
                        $faker->randomElement([
                            'paid',
                            'paid',
                            'paid',
                            'paid',
                            'pending',
                            'cancelled'
                        ]),

                    'created_at' =>
                        $faker->dateTimeBetween('-2 months', 'now'),

                    'updated_at' => now(),
                ];
            }

            // Masukkan ke database sekaligus agar super cepat
            foreach (array_chunk($transactions, 100) as $chunk) {
                DB::table('transactions')->insert($chunk);
            }

            DB::table('transactions')->insert([

                [
                    'user_id' => 2,
                    'ticket_id' => 1,
                    'transaction_code' => 'TRX-PREMIUM-001',
                    'quantity' => 2,
                    'total_price' => 2 * $tickets[1]->price,
                    'visit_date' => now(),
                    'payment_status' => 'paid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'user_id' => 2,
                    'ticket_id' => 2,
                    'transaction_code' => 'TRX-VIP-002',
                    'quantity' => 1,
                    'total_price' => 1 * $tickets[2]->price,
                    'visit_date' => now(),
                    'payment_status' => 'paid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            ]);

            echo "\n✅ Artworks Asli Berhasil Diimport!";
            echo "\n✅ 1000 Transaksi Palsu Berhasil Dibuat untuk Presentasi!\n";
        }
    }
}