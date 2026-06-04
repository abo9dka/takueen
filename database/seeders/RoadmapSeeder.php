<?php

namespace Database\Seeders;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoadmapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roadmaps = [
            [
                'title' => 'Algorithms Roadmap',
                'description' => 'Complete algorithms learning roadmap',
                'field_id' => 1,
            ],
            [
                'title' => 'Data Structures Roadmap',
                'description' => 'Complete data structures learning roadmap',
                'field_id' => 2,
            ],
            [
                'title' => 'Graphs Roadmap',
                'description' => 'Complete graph algorithms roadmap',
                'field_id' => 3,
            ],
            [
                'title' => 'Dynamic Programming Roadmap',
                'description' => 'Complete dynamic programming roadmap',
                'field_id' => 4,
            ],
        ];

        foreach ($roadmaps as $roadmap) {

            $supervisor = User::where('role', 'supervisor')
                ->inRandomOrder()
                ->first();

            Roadmap::create([
                'title' => $roadmap['title'],
                'description' => $roadmap['description'],
                'ai_generated' => false,
                'field_id' => $roadmap['field_id'],
                'user_id' => $supervisor->id,
            ]);
        }
    }
}
