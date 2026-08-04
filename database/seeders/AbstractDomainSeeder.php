<?php

namespace Database\Seeders;

use App\Models\AbstractDomain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbstractDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $abstractDomains = [
            [
                'domain' => 'Environment and Earth Science',
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'domain' => 'Engineering and Technology',
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'domain' => 'Life and Basic Science',
                'status' => 1,
                'sort_order' => 3,
            ],
            [
                'domain' => 'Social Science',
                'status' => 1,
                'sort_order' => 4,
            ],
            [
                'domain' => 'Cultural Innovation',
                'status' => 1,
                'sort_order' => 5,
            ]
        ];
        
        foreach($abstractDomains as $domain){
            AbstractDomain::updateOrCreate(
                ['domain' => $domain['domain']],
                $domain
            );
        }
    }
}
