<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PaymentMethodSeeder::class,
            WinnersTableSeeder::class,
            CsvWinnersSeeder::class,
            InitialContentSeeder::class,
            CsvWinnersBatchSeeder::class,
            DemoWinnerSeeder::class,
        ]);
    }
}
