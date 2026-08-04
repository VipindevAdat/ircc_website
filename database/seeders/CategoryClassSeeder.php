<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category_classes')->insert([
            [   
                'category_id' => 1,
                'class' => 1, 
                'status'=>1
            ],
            [   
                'category_id' => 1,
                'class' => 2, 
                'status'=>1
            ],
            [   
                'category_id' => 1,
                'class' => 3, 
                'status'=>1
            ],
             [   
                'category_id' => 1,
                'class' => 4, 
                'status'=>1
            ],
            [   
                'category_id' => 1,
                'class' => 5, 
                'status'=>1
            ],
            [   
                'category_id' => 2,
                'class' => 6, 
                'status'=>1
            ],
            [   
                'category_id' => 2,
                'class' => 7, 
                'status'=>1
            ],
            [   
                'category_id' => 2,
                'class' => 8, 
                'status'=>1
            ],
            [   
                'category_id' => 3,
                'class' => 9, 
                'status'=>1
            ],
            [   
                'category_id' => 3,
                'class' => 10, 
                'status'=>1
            ],
            [   
                'category_id' => 3,
                'class' => 11, 
                'status'=>1
            ],
            [   
                'category_id' => 3,
                'class' => 12, 
                'status'=>1
            ],
         ]);
    }
}
