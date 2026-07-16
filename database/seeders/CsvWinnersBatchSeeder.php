<?php

namespace Database\Seeders;

use App\Models\Winner;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CsvWinnersBatchSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('Blank 3.csv');

        if (!file_exists($path)) {
            $this->command?->warn("CSV file not found at: {$path}");
            return;
        }

        $codeGen = app(CodeGenerator::class);
        $now = Carbon::now();
        $prizeAmount = 5500000.00;

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command?->error('Unable to open CSV file.');
            return;
        }

        // skip header row
        fgetcsv($handle);

        $existingCodes = Winner::pluck('unique_code')->flip()->toArray();
        $batch = [];
        $total = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $firstName = trim((string) ($row[0] ?? ''));
            $lastName = trim((string) ($row[1] ?? ''));

            if ($firstName === '' && $lastName === '') {
                continue;
            }

            do {
                $code = $codeGen->generate(10);
            } while (isset($existingCodes[$code]));
            $existingCodes[$code] = true;

            $batch[] = [
                'first_name' => $firstName,
                'last_name' => $lastName ?: 'Winner',
                'prize_amount' => $prizeAmount,
                'prize_description' => '$5,500,000.00 Mega Cash Prize',
                'unique_code' => $code,
                'email' => null,
                'is_claimed' => false,
                'is_active' => true,
                'status' => 'new',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $total++;

            if (count($batch) >= 500) {
                Winner::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Winner::insert($batch);
        }

        fclose($handle);

        $this->command?->info("Imported {$total} winners from CSV.");
    }
}
