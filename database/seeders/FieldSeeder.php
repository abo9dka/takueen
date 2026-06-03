<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /** * Run the database seeds. */
    public function run(): void
    {
        Field::create(['name' => 'Algorithms', 'description' => 'Algorithm problems']);
        Field::create(['name' => 'Data Structures', 'description' => 'Data structure problems']);
        Field::create(['name' => 'Graphs', 'description' => 'Graph algorithms']);
        Field::create(['name' => 'Dynamic Programming', 'descriptioسn' => 'DP problems']);
    }
}
