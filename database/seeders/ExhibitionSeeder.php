<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use App\Models\Artwork;
use Carbon\Carbon;

class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exhibitions = [

            /*
            |--------------------------------------------------------------------------
            | PAST EXHIBITIONS
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'A New Look at Jan van Eyck',

                'subtitle' =>
                    'The Madonna of Chancellor Rolin',

                'description' =>
                    'An unprecedented exhibition structured around the recent conservation work on the Madonna of Chancellor Rolin, offering a new perspective on Jan van Eyck masterpiece.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/styles/w1920_h823_c1/public/2024-02/jan-van-eyck-la-vierge-du-chancelier-rolin-vers-1430-huile-sur-bois-chene-paris-musee-du-louvre-inv-1271.jpg',

                'start_date' => '2024-03-20',

                'end_date' => '2024-06-17',

                'museum_id' => 9,
            ],

            [
                'title' => 'The Treasury of Notre-Dame Cathedral',

                'subtitle' =>
                    'From Its Origins to Viollet-Le-Duc',

                'description' =>
                    'An exceptional exhibition featuring over 120 works from the treasury of Notre-Dame Cathedral in Paris, tracing its history prior to the cathedral reopening.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/styles/w1920_h823_c1/public/2023-10/placide-poussielgue-rusand-d-apres-eugene-viollet-le-duc-reliquaire-du-clou-et-du-bois-de-la-croix-tresor-de-notre-dame-de-paris.jpg',

                'start_date' => '2023-10-18',

                'end_date' => '2024-01-29',

                'museum_id' => 9,
            ],

            [
                'title' => 'Naples in Paris',

                'subtitle' =>
                    'The Louvre Hosts the Museo di Capodimonte',

                'description' =>
                    'An exceptional partnership bringing masterpieces from the Museo di Capodimonte in Naples to the Louvre, showcasing one of the most important collections in Italy.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/styles/w1920_h823_c1/public/2023-02/francesco-mazzola-dit-il-parmigianino-portrait-de-jeune-femme-dit-aussi-antea.jpg',

                'start_date' => '2023-06-07',

                'end_date' => '2024-01-08',

                'museum_id' => 9,
            ],

            [
                'title' => 'Things',

                'subtitle' =>
                    'A History of Still Life',

                'description' =>
                    'An authorial exhibition proposing a new perspective on still life painting and the representation of objects, from prehistoric times to the present day.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/styles/w1734_h743_c1/public/2022-07/1-Luis_Egidio-Melendez-Nature_morte_avec_pasteques.jpg',

                'start_date' => '2022-10-12',

                'end_date' => '2023-01-23',

                'museum_id' => 9,
            ],

            [
                'title' => 'Pharaoh of the Two Lands',

                'subtitle' =>
                    'The African Story of the Kings of Napata',

                'description' =>
                    'The epic saga of the Kushite kings who conquered Egypt and founded the 25th Dynasty, ruling over a vast territory from the Nile Delta to the confluence of the White and Blue Niles.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/styles/w1920_h823_c1/public/2022-02/affiche-de-l-exposition-pharaon-des-deux-terres-l-epopee-africaine-des-rois-de-napata.jpg',

                'start_date' => '2022-04-28',

                'end_date' => '2022-07-25',

                'museum_id' => 9,
            ],

            /*
            |--------------------------------------------------------------------------
            | CURRENT & UPCOMING EXHIBITIONS
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Martin Schongauer',

                'subtitle' =>
                    'The Beautiful Immortal',

                'description' =>
                    'Through roughly a hundred assembled pieces, the exhibition highlights the remarkable legacy of Martin Schongauer, one of the major artistic figures of the late Middle Ages.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/2025-04/martin-schongauer.jpg',

                'start_date' => '2026-04-08',

                'end_date' => '2026-07-20',

                'museum_id' => 9,
            ],

            [
                'title' => 'Michelangelo / Rodin',

                'subtitle' =>
                    'Living Bodies',

                'description' =>
                    'Two unrivalled masters of Western sculpture engage in a dialogue across the centuries: Michelangelo and Rodin.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/2025-04/michelangelo-rodin.jpg',

                'start_date' => '2026-04-15',

                'end_date' => '2026-07-20',

                'museum_id' => 9,
            ],

            [
                'title' => 'Primeval Waters',

                'subtitle' =>
                    'Lessons from Mesopotamia',

                'description' =>
                    'An exhibition exploring ancient Mesopotamian relationships with water, hydraulic systems, and environmental sustainability.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/2025-04/primeval-waters.jpg',

                'start_date' => '2026-05-20',

                'end_date' => '2027-03-15',

                'museum_id' => 9,
            ],

            [
                'title' => 'Jacques-Louis David',

                'subtitle' => null,

                'description' =>
                    'A major retrospective celebrating the artistic and political journey of Jacques-Louis David.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/2025-04/jacques-louis-david.jpg',

                'start_date' => '2025-09-01',

                'end_date' => '2026-12-26',

                'museum_id' => 9,
            ],

            [
                'title' => 'The Carracci Drawings',

                'subtitle' =>
                    'The Making of the Farnese Gallery',

                'description' =>
                    'An immersive exploration of the preparatory drawings behind one of the greatest masterpieces of Western painting.',

                'banner_image' =>
                    'https://api-www.louvre.fr/sites/default/files/2025-04/carracci-drawings.jpg',

                'start_date' => '2025-11-01',

                'end_date' => '2026-12-02',

                'museum_id' => 9,
            ],

        ];

        foreach ($exhibitions as $data) {

            /*
            |--------------------------------------------------------------------------
            | AUTO STATUS GENERATION
            |--------------------------------------------------------------------------
            */

            $today = Carbon::today();

            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if ($today->lt($startDate)) {

                $status = 'Upcoming';

            } elseif ($today->gt($endDate)) {

                $status = 'Past';

            } else {

                $status = 'Current';
            }

            $data['status'] = $status;

            /*
            |--------------------------------------------------------------------------
            | CREATE EXHIBITION
            |--------------------------------------------------------------------------
            */

            $exhibition = Exhibition::create($data);

            $title = $data['title'];
            $subtitle = $data['subtitle'] ?? '';

            /*
            |--------------------------------------------------------------------------
            | AUTO ATTACH ARTWORKS
            |--------------------------------------------------------------------------
            */

            if (
                str_contains($title, 'Pharaoh') ||
                str_contains($subtitle, 'Napata')
            ) {

                // Egyptian / Ancient artworks

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Egypt%')
                        ->orWhere('name', 'like', '%Ancient%')
                        ->orWhere('name', 'like', '%Near Eastern%');

                });

            } elseif (
                str_contains($title, 'Michelangelo') ||
                str_contains($title, 'Rodin')
            ) {

                // Sculpture exhibition

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Sculpture%');

                });

            } elseif (
                str_contains($title, 'Primeval Waters')
            ) {

                // Mesopotamian / Near Eastern exhibition

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Near Eastern%');

                });

            } elseif (
                str_contains($title, 'Things')
            ) {

                // Still life / painting exhibition

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Painting%');

                });

            } elseif (
                str_contains($title, 'Naples')
            ) {

                // European masterpieces

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Painting%')
                        ->orWhere('name', 'like', '%Decorative%');

                });

            } elseif (
                str_contains($title, 'Jan van Eyck')
            ) {

                // Renaissance / religious paintings

                $artworks = Artwork::whereHas('category', function ($q) {

                    $q->where('name', 'like', '%Painting%');

                });

            } else {

                // Smart fallback

                $artworks = Artwork::whereNotNull('category_id');
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL CURATED FILTER
            |--------------------------------------------------------------------------
            */

            $artworkIds = $artworks
                ->whereNotNull('image_url')
                ->whereNotNull('description')
                ->where('description', '!=', '')
                ->where('description', '!=', 'Imported from API')
                ->inRandomOrder()
                ->take(12)
                ->pluck('id');

            /*
            |--------------------------------------------------------------------------
            | ATTACH TO EXHIBITION
            |--------------------------------------------------------------------------
            */

            $exhibition->artworks()->attach($artworkIds);
        }
    }
}