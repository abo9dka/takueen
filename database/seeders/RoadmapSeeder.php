<?php

namespace Database\Seeders;

use App\Models\Roadmap;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoadmapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Roadmap::create(['title' => 'LARAVEL', 'description' => 'Complete laravel roadmap','ai_generated' => false, 'field_id' => 1]);
        Roadmap::create(['title' => 'REACT', 'description' => 'Complete react roadmap','ai_generated' => false, 'field_id' => 2]);
    }
}