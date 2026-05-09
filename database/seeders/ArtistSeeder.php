<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtistSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('artists')->insert([
            [
                'name' => 'Leonardo da Vinci',
                'bio' => 'Italian Renaissance polymath known for masterpieces such as the Mona Lisa and The Last Supper.'
            ],
            [
                'name' => 'Vincent van Gogh',
                'bio' => 'Dutch Post-Impressionist painter famous for expressive brushwork and emotional depth.'
            ],
            [
                'name' => 'Claude Monet',
                'bio' => 'Founder of French Impressionist painting known for landscapes and water lilies.'
            ],
            [
                'name' => 'Michelangelo',
                'bio' => 'Italian sculptor, painter, and architect of the High Renaissance.'
            ],
            [
                'name' => 'Rembrandt van Rijn',
                'bio' => 'Dutch Golden Age painter celebrated for portraits and dramatic use of light.'
            ],
            [
                'name' => 'Raden Saleh',
                'bio' => 'Indonesian Romantic painter recognized as a pioneer of modern Indonesian art.'
            ],
            [
                'name' => 'Affandi',
                'bio' => 'Indonesian expressionist painter famous for emotional and spontaneous painting techniques.'
            ],
            [
                'name' => 'Pablo Picasso',
                'bio' => 'Spanish artist and co-founder of the Cubist movement.'
            ],
            [
                'name' => 'Johannes Vermeer',
                'bio' => 'Dutch Baroque painter known for intimate domestic interior scenes.'
            ],
            [
                'name' => 'Unknown Ancient Egyptian Artisan',
                'bio' => 'Anonymous craftsman responsible for ceremonial artifacts and royal relics in ancient Egypt.'
            ],
            [
                'name' => 'Unknown Classical Greek Sculptor',
                'bio' => 'Anonymous sculptor from ancient Greece specializing in marble statues and mythological works.'
            ],
            [
                'name' => 'Basuki Abdullah',
                'bio' => 'Indonesian painter known for portraits and realistic paintings.'
            ]
        ]);
    }
}