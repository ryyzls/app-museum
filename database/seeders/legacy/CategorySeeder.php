<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Renaissance Painting'],
            ['name' => 'Impressionist Painting'],
            ['name' => 'Modern Painting'],
            ['name' => 'Classical Sculpture'],
            ['name' => 'Ancient Egyptian Artifact'],
            ['name' => 'Greek and Roman Antiquities'],
            ['name' => 'Islamic Art'],
            ['name' => 'Asian Ceramics'],
            ['name' => 'Indonesian Heritage Artifact'],
            ['name' => 'Textile and Manuscript'],
            ['name' => 'Photography'],
            ['name' => 'Fossil and Natural History'],
        ]);
    }
}