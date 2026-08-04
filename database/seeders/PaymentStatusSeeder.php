<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentStatus;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['id'=>1,
            'status'=>'Pending'], 
            ['id'=>2,
            'status'=>'Processing'], 
            ['id'=>3,
            'status'=>'Completed'], 
            ['id'=>4,
             'status'=>'Cancelled'], 
            ['id'=>5,
             'status'=>'Refunded'], 
            ['id'=>6,
             'status'=>'Failed']
           ];

        foreach ($statuses as $status) {
            PaymentStatus::updateOrCreate($status);
        }
    }
}
