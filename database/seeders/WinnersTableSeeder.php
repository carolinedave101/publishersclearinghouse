<?php

namespace Database\Seeders;

use App\Models\Winner;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WinnersTableSeeder extends Seeder
{
    public function run(): void
    {
        $codeGen = app(CodeGenerator::class);

        $winners = [
            ['first_name' => 'Pearl', 'last_name' => 'Barley', 'email' => 'pearl.barley@pch.com', 'address' => '1608 Creekside Dr', 'city' => 'Hoover', 'state' => 'AL', 'zip' => '35205', 'prize_amount' => 5000, 'prize_description' => '$5,000 Cash Prize - Summer SuperPrize'],
            ['first_name' => 'James', 'last_name' => 'Mitchell', 'email' => 'james.mitchell@pch.com', 'address' => '742 Evergreen Terrace', 'city' => 'Portland', 'state' => 'OR', 'zip' => '97201', 'prize_amount' => 10000, 'prize_description' => '$10,000 Cash Prize'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.johnson@pch.com', 'address' => '456 Oak Ave', 'city' => 'Miami', 'state' => 'FL', 'zip' => '33101', 'prize_amount' => 25000, 'prize_description' => '$25,000 Dream Vacation Package'],
            ['first_name' => 'Robert', 'last_name' => 'Williams', 'email' => 'robert.williams@pch.com', 'address' => '789 Pine Rd', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75201', 'prize_amount' => 50000, 'prize_description' => '$50,000 Home Makeover'],
            ['first_name' => 'Emily', 'last_name' => 'Brown', 'email' => 'emily.brown@pch.com', 'address' => '321 Elm St', 'city' => 'Chicago', 'state' => 'IL', 'zip' => '60601', 'prize_amount' => 7500, 'prize_description' => '$7,500 Shopping Spree'],
            ['first_name' => 'Michael', 'last_name' => 'Davis', 'email' => 'michael.davis@pch.com', 'address' => '654 Maple Dr', 'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85001', 'prize_amount' => 15000, 'prize_description' => '$15,000 Tech Bundle'],
            ['first_name' => 'Jennifer', 'last_name' => 'Garcia', 'email' => 'jennifer.garcia@pch.com', 'address' => '987 Cedar Ln', 'city' => 'Los Angeles', 'state' => 'CA', 'zip' => '90001', 'prize_amount' => 2000, 'prize_description' => '$2,000 Gift Card'],
            ['first_name' => 'David', 'last_name' => 'Martinez', 'email' => 'david.martinez@pch.com', 'address' => '147 Birch Ct', 'city' => 'Houston', 'state' => 'TX', 'zip' => '77001', 'prize_amount' => 100000, 'prize_description' => '$100,000 Mega Cash Prize'],
            ['first_name' => 'Lisa', 'last_name' => 'Anderson', 'email' => 'lisa.anderson@pch.com', 'address' => '258 Walnut Way', 'city' => 'Seattle', 'state' => 'WA', 'zip' => '98101', 'prize_amount' => 35000, 'prize_description' => '$35,000 Luxury SUV'],
            ['first_name' => 'Thomas', 'last_name' => 'Taylor', 'email' => 'thomas.taylor@pch.com', 'address' => '369 Spruce St', 'city' => 'Denver', 'state' => 'CO', 'zip' => '80201', 'prize_amount' => 500, 'prize_description' => '$500 Bonus Cash'],
            ['first_name' => 'Amanda', 'last_name' => 'Thomas', 'email' => 'amanda.thomas@pch.com', 'address' => '159 Park Ave', 'city' => 'Boston', 'state' => 'MA', 'zip' => '02101', 'prize_amount' => 12000, 'prize_description' => '$12,000 Furniture Makeover'],
            ['first_name' => 'Christopher', 'last_name' => 'Jackson', 'email' => 'christopher.jackson@pch.com', 'address' => '753 Lake Dr', 'city' => 'Atlanta', 'state' => 'GA', 'zip' => '30301', 'prize_amount' => 8000, 'prize_description' => '$8,000 Outdoor Living Package'],
            ['first_name' => 'Michelle', 'last_name' => 'White', 'email' => 'michelle.white@pch.com', 'address' => '951 River Rd', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37201', 'prize_amount' => 40000, 'prize_description' => '$40,000 Kitchen Renovation'],
        ];

        $now = Carbon::now();

        $prizeAmount = 5500000.00;

        foreach ($winners as $i => $data) {
            $data['prize_amount'] = $prizeAmount;
            $data['prize_description'] = '$5,500,000.00 Mega Cash Prize';
            $data['unique_code'] = $codeGen->generateUniqueCode();
            $data['is_claimed'] = $i < 5;
            $data['claimed_at'] = $i < 5 ? $now->copy()->subDays($i * 2) : null;
            $data['created_at'] = $now->copy()->subDays(30 - $i);
            $data['updated_at'] = $now->copy()->subDays(30 - $i);

            Winner::create($data);
        }
    }
}
