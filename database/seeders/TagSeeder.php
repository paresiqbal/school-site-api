<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tags = ['Berita', 'Pengumuman', 'Acara', 'Update', 'Informasi', 'Ujian', 'Rapat', 'Ceramah', 'Olahraga'];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
    }
}
