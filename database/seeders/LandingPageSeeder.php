<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::firstOrCreate(
            ['year' => 2026],
            ['title' => 'IRCC 2026', 'is_active' => true]
        );
    }
}
