<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtworkSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('artworks')->insert([

            [
                'title' => 'Mona Lisa',
                'description' => 'A portrait painting by Leonardo da Vinci, considered one of the most famous artworks in history.',
                'image' => '/images/artworks/monalisa.jpg',
                'artist_id' => 1,
                'category_id' => 1,
                'museum_id' => 1,
            ],

            [
                'title' => 'The Last Supper',
                'description' => 'A late 15th-century mural painting representing the final meal of Jesus with his apostles.',
                'image' => '/images/artworks/the_last_supper.jpg',
                'artist_id' => 1,
                'category_id' => 1,
                'museum_id' => 7,
            ],

            [
                'title' => 'Starry Night',
                'description' => 'An iconic Post-Impressionist painting depicting a swirling night sky over a quiet village.',
                'image' => '/images/artworks/starry_night.jpg',
                'artist_id' => 2,
                'category_id' => 2,
                'museum_id' => 3,
            ],

            [
                'title' => 'Sunflowers',
                'description' => 'A still life painting series featuring vibrant sunflowers in a vase.',
                'image' => '/images/artworks/sunflowers.jpg',
                'artist_id' => 2,
                'category_id' => 2,
                'museum_id' => 6,
            ],

            [
                'title' => 'Water Lilies',
                'description' => 'A famous series of Impressionist paintings inspired by Monet’s garden in Giverny.',
                'image' => '/images/artworks/water_lilies.jpg',
                'artist_id' => 3,
                'category_id' => 2,
                'museum_id' => 1,
            ],

            [
                'title' => 'David',
                'description' => 'A Renaissance marble sculpture representing the Biblical hero David.',
                'image' => '/images/artworks/david.jpg',
                'artist_id' => 4,
                'category_id' => 4,
                'museum_id' => 7,
            ],

            [
                'title' => 'The Night Watch',
                'description' => 'A celebrated Dutch Golden Age painting known for its dramatic lighting.',
                'image' => '/images/artworks/the_night_watch.jpg',
                'artist_id' => 5,
                'category_id' => 3,
                'museum_id' => 6,
            ],

            [
                'title' => 'The Arrest of Prince Diponegoro',
                'description' => 'A historic painting portraying the capture of Prince Diponegoro during colonial Indonesia.',
                'image' => '/images/artworks/the_arrest_of_prince_diponegoro.jpg',
                'artist_id' => 6,
                'category_id' => 3,
                'museum_id' => 5,
            ],

            [
                'title' => 'Self Portrait by Affandi',
                'description' => 'An expressive self portrait showcasing Affandi’s emotional painting technique.',
                'image' => '/images/artworks/self_portrait_by_affandi.jpg',
                'artist_id' => 7,
                'category_id' => 3,
                'museum_id' => 4,
            ],

            [
                'title' => 'Guernica',
                'description' => 'A monumental anti-war painting inspired by the bombing of Guernica during the Spanish Civil War.',
                'image' => '/images/artworks/guernica.jpg',
                'artist_id' => 8,
                'category_id' => 3,
                'museum_id' => 3,
            ],

            [
                'title' => 'Girl with a Pearl Earring',
                'description' => 'A renowned portrait often referred to as the Dutch Mona Lisa.',
                'image' => '/images/artworks/girl_with_a_pearl_earring.jpg',
                'artist_id' => 9,
                'category_id' => 3,
                'museum_id' => 6,
            ],

            [
                'title' => 'Golden Sarcophagus Fragment',
                'description' => 'Ancient Egyptian funerary artifact decorated with symbolic carvings and gold plating.',
                'image' => '/images/artworks/golden_sarcophagus_fragment.jpg',
                'artist_id' => 10,
                'category_id' => 5,
                'museum_id' => 8,
            ],

            [
                'title' => 'Marble Statue of Athena',
                'description' => 'Classical Greek sculpture depicting the goddess Athena in ceremonial armor.',
                'image' => '/images/artworks/marble_statue_of_athena.jpg',
                'artist_id' => 11,
                'category_id' => 6,
                'museum_id' => 2,
            ],

            [
                'title' => 'Portrait of a Balinese Dancer',
                'description' => 'A realistic portrait capturing traditional Balinese cultural attire and expression.',
                'image' => '/images/artworks/portrait_of_a_balinese_dancer.jpg',
                'artist_id' => 12,
                'category_id' => 3,
                'museum_id' => 5,
            ],

        ]);
    }
}