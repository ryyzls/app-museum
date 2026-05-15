<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Museum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class LouvreSeeder extends Seeder
{
    public function run(): void
    {
        $museum = Museum::firstOrCreate(
            ['name' => 'Musée du Louvre'],
            [
                'location' => 'Paris, France',
                'description' => 'The Louvre Museum'
            ]
        );

        $csvPath = database_path('seeders/csv/louvre_ark.csv');

        // delimiter ;
        $rows = array_map(function ($line) {
            return str_getcsv($line, ',');
        }, file($csvPath));
        array_shift($rows);

        // test 100 dulu
        $rows = array_slice($rows, 0, 4999);

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {

            try {

                $arkId = trim($row[0]);

                $cleanArk = str_replace(
                    'ark:/53355/',
                    '',
                    $arkId
                );

                $url = "https://collections.louvre.fr/ark:/53355/{$cleanArk}.json";

                $response = Http::timeout(15)->get($url);

                if (!$response->successful()) {

                    $skipped++;
                    continue;
                }

                $data = $response->json();

                $title =
                    $data['title']
                    ?? $row[1]
                    ?? 'Unknown Artwork';


                // ambil artist dari API dulu, fallback CSV
                $author =
                    $data['author'][0]['label']
                    ?? $row[2]
                    ?? 'Anonymous';


                // ambil kategori
                $collection =
                    $row[3]
                    ?? 'General';


                // Mapping kategori
                $categoryMap = [

                    'Département des Peintures'
                    => 'Paintings',

                    'Département des Sculptures du Moyen Age, de la Renaissance et des temps modernes'
                    => 'Sculptures',

                    'Département des Antiquités égyptiennes'
                    => 'Egyptian',

                    'Département des Antiquités grecques, étrusques et romaines'
                    => 'Greek/Roman',

                    'Département des Objets d\'art du Moyen Age, de la Renaissance et des temps modernes'
                    => 'Decorative Arts',

                    'Département des Arts de l\'Islam'
                    => 'Islamic Arts'
                ];

                $collection =
                    $categoryMap[$collection]
                    ?? $collection;


                // cari gambar di seluruh JSON
                $imageUrl = null;

                array_walk_recursive(
                    $data,
                    function ($value, $key) use (&$imageUrl) {

                        if (
                            $key === 'urlImage'
                            &&
                            !$imageUrl
                        ) {

                            $imageUrl = $value;

                        }

                    }
                );


                // cari deskripsi di seluruh JSON
                $description = 'No description available';

                array_walk_recursive(
                    $data,
                    function ($value, $key) use (&$description) {

                        if (
                            in_array(
                                strtolower($key),
                                [
                                    'description',
                                    'content',
                                    'summary',
                                    'comment'
                                ]
                            )
                            &&
                            $description === 'No description available'
                            &&
                            is_string($value)
                            &&
                            strlen(trim($value)) > 30
                        ) {

                            $description = strip_tags($value);

                        }

                    }
                );


                // QA ringan
                if (strlen($title) < 5) {

                    $skipped++;
                    continue;
                }

                $badWords = [

                    'pot',
                    'jarre',
                    'bol',
                    'gourde',
                    'fragment'

                ];

                foreach ($badWords as $word) {

                    if (
                        str_contains(
                            strtolower($title),
                            strtolower($word)
                        )
                    ) {

                        $skipped++;
                        continue 2;

                    }

                }


                $artist = Artist::firstOrCreate(

                    ['name' => $author],

                    [
                        'bio' => 'Artist imported from Louvre Collection'
                    ]
                );

                $category = Category::firstOrCreate([

                    'name' => $collection

                ]);


                Artwork::firstOrCreate(

                    ['ark_id' => $cleanArk],

                    [

                        'title' => $title,

                        'description' => $description,

                        'image_url' => $imageUrl,

                        'artist_id' => $artist->id,

                        'category_id' => $category->id,

                        'museum_id' => $museum->id

                    ]

                );

                $imported++;

            } catch (\Exception $e) {

                $skipped++;

            }

        }

        dump("Imported: " . $imported);
        dump("Skipped: " . $skipped);

    }
}