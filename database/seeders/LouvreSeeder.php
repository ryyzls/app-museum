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
        $rows = array_slice($rows, 0, 150);

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



                /*
                |--------------------------------------------------------------------------
                | ARTIST NORMALIZATION
                |--------------------------------------------------------------------------
                */

                $artistName =
                    trim(
                        $data['author'][0]['label']
                        ?? $row[2]
                        ?? ''
                    );

                $invalidArtistPatterns = [

                    'paire',
                    'aigle',
                    'foie',
                    'fragment',
                    'sans titre',
                    'unknown',
                    'non identifié'

                ];

                if (

                    empty($artistName)
                    ||
                    strlen($artistName) < 3
                    ||
                    strlen($artistName) > 80

                ) {

                    $artistName = 'Unknown Artist';

                }

                foreach ($invalidArtistPatterns as $pattern) {

                    if (
                        str_contains(
                            strtolower($artistName),
                            strtolower($pattern)
                        )
                    ) {

                        $artistName = 'Unknown Artist';
                        break;

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | CATEGORY NORMALIZATION
                |--------------------------------------------------------------------------
                */

                $collection =
                    strtolower(
                        trim($row[3] ?? '')
                    );

                $normalizedCategory = 'General Collection';

                if (

                    str_contains($collection, 'peinture')
                    ||
                    str_contains($collection, 'painting')

                ) {

                    $normalizedCategory = 'Paintings';

                } elseif (

                    str_contains($collection, 'sculpture')

                ) {

                    $normalizedCategory = 'Sculptures';

                } elseif (

                    str_contains($collection, 'dessin')
                    ||
                    str_contains($collection, 'drawing')

                ) {

                    $normalizedCategory = 'Drawings';

                } elseif (

                    str_contains($collection, 'islam')

                ) {

                    $normalizedCategory = 'Islamic Arts';

                } elseif (

                    str_contains($collection, 'grec')
                    ||
                    str_contains($collection, 'romain')

                ) {

                    $normalizedCategory = 'Greek & Roman Arts';

                } elseif (

                    str_contains($collection, 'egypt')

                ) {

                    $normalizedCategory = 'Egyptian Antiquities';

                } elseif (

                    str_contains($collection, 'objet')
                    ||
                    str_contains($collection, 'decorative')

                ) {

                    $normalizedCategory = 'Decorative Arts';

                } elseif (

                    str_contains($collection, 'textile')

                ) {

                    $normalizedCategory = 'Textiles';

                } elseif (

                    str_contains($collection, 'bijoux')
                    ||
                    str_contains($collection, 'jewelry')

                ) {

                    $normalizedCategory = 'Jewelry';

                } elseif (

                    str_contains($collection, 'ceramique')
                    ||
                    str_contains($collection, 'ceramic')

                ) {

                    $normalizedCategory = 'Ceramics';

                }


                /*
                |--------------------------------------------------------------------------
                | ARTIST CREATION
                |--------------------------------------------------------------------------
                */

                $artist = Artist::firstOrCreate(

                    ['name' => $artistName],

                    [

                        'bio' => $artistName === 'Unknown Artist'

                            ? 'Anonymous artist featured in the Louvre Collection.'

                            : $artistName . ' is an artist featured in the Louvre Collection.'

                    ]

                );


                /*
                |--------------------------------------------------------------------------
                | CATEGORY CREATION
                |--------------------------------------------------------------------------
                */

                $category = Category::firstOrCreate([

                    'name' => $normalizedCategory

                ]);

                /*
                |--------------------------------------------------------------------------
                | IMAGE EXTRACTION
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | DESCRIPTION EXTRACTION
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | LIGHT QA FILTER
                |--------------------------------------------------------------------------
                */

                if (strlen($title) < 3) {

                    $skipped++;
                    continue;
                }

                if (!$imageUrl) {

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