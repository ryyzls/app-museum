<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuseumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('museums')->insert([
            [
                'name' => 'Louvre Museum',
                'location' => 'Paris, France',
                'description' => 'The world’s largest art museum, home to thousands of classical and Renaissance masterpieces including the Mona Lisa.'
            ],
            [
                'name' => 'British Museum',
                'location' => 'London, United Kingdom',
                'description' => 'A historic museum dedicated to human history, archaeology, and world cultures.'
            ],
            [
                'name' => 'The Metropolitan Museum of Art',
                'location' => 'New York City, United States',
                'description' => 'One of the most prestigious art museums in the world with collections spanning over 5,000 years.'
            ],
            [
                'name' => 'Museum MACAN',
                'location' => 'Jakarta, Indonesia',
                'description' => 'Museum of Modern and Contemporary Art in Nusantara focusing on modern and contemporary works.'
            ],
            [
                'name' => 'National Museum of Indonesia',
                'location' => 'Jakarta, Indonesia',
                'description' => 'Indonesia’s leading museum preserving archaeological, ethnographic, and historical collections.'
            ],
            [
                'name' => 'Rijksmuseum',
                'location' => 'Amsterdam, Netherlands',
                'description' => 'Dutch national museum dedicated to arts, crafts, and history from the Golden Age.'
            ],
            [
                'name' => 'Uffizi Gallery',
                'location' => 'Florence, Italy',
                'description' => 'A renowned Italian museum housing masterpieces from the Renaissance period.'
            ],
            [
                'name' => 'Egyptian Museum',
                'location' => 'Cairo, Egypt',
                'description' => 'Museum containing the world’s most extensive collection of ancient Egyptian antiquities.'
            ]
        ]);
    }
}