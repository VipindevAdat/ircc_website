<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewStatus;

class ReviewStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['Under Review', 'Approved', 'Rejected'];

        foreach ($statuses as $status) {
            ReviewStatus::updateOrCreate(['status' => $status]);
        }
    }
}
