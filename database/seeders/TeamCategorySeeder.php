<?php

namespace Database\Seeders;

use App\Models\TeamCategory as ModelsTeamCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class TeamCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'=>'COMMITTEE',
                'status' => 1,
            ],
            [
                'name'=>'SPEAKERS',
                'status' => 1,
            ],
            ];
            foreach($categories as $category){
                ModelsTeamCategory::updateOrCreate($category);
            }
    }
}
