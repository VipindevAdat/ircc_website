<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update or insert categories with new names and grade ranges
        $categories = [
            [
                'id' => 1,
                'name' => 'Elementary School', 
                'prefix' => 'E',
                'last_number' => 0,
                'type' => 2,
                'grade_from' => 1,
                'grade_to' => 5,
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'id' => 2,
                'name' => 'Middle School', 
                'prefix' => 'M',
                'last_number' => 0,
                'type' => 2,
                'grade_from' => 6,
                'grade_to' => 8,
                'status' => 1,
                'sort_order' => 2
            ],
            [
                'id' => 3,
                'name' => 'High School', 
                'prefix' => 'H',
                'last_number' => 0,
                'type' => 2,
                'grade_from' => 9,
                'grade_to' => 12,
                'status' => 1,
                'sort_order' => 3
            ],
            [
                'id' => 4,
                'name' => 'Special School', 
                'prefix' => 'O',
                'last_number' => 0,
                'type' => 1,
                'grade_from' => null,
                'grade_to' => null,
                'status' => 1,
                'sort_order' => 4
            ]
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                $category
            );
        }
    }
}
