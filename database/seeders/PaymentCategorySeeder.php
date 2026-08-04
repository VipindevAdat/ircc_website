<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentCategory;

class PaymentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_id'=>'01',
            'category'=>'Netbanking'], 
            ['category_id'=>'02',
            'category'=>'Credit Card'], 
            ['category_id'=>'03',
            'category'=>'Debit Card'], 
            ['category_id'=>'04',
             'category'=>'Cash Card'], 
            ['category_id'=>'05',
             'category'=>'Wallet'], 
            ['category_id'=>'07',
             'category'=>'Reward Points'],
            ['category_id'=>10,
            'category'=>' UPI: Bank Account'], 
            ['category_id'=>11,
            'category'=>'BharatQR'], 
            ['category_id'=>12,
            'category'=>'- Loan EMI'], 
            ['category_id'=>13,
             'category'=>'NEFT'], 
            ['category_id'=>18,
             'category'=>'UPI: Credit Card'], 
            ['category_id'=>19,
             'category'=>'NACH'],
             ['category_id'=>20,
             'category'=>'CBDC'], 
            ['category_id'=>21,
             'category'=>'UPI: Prepaid Wallet']
           ];

        foreach ($categories as $category) {
            PaymentCategory::updateOrCreate($category);
        }
    }
}
