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
        $this->importCsv(base_path('Trump.csv'), 'Trump.csv');
        $this->importCsv(base_path('Blank 4.csv'), 'Blank 4.csv');
    }

    private function importCsv(string $path, string $label): void
    {
        if (!file_exists($path)) {
            $this->command?->warn("CSV file not found: {$path}");
            return;
        }

        $codeGen = app(CodeGenerator::class);
        $now = Carbon::now();
        $prizeAmount = 5500000.00;

        $handle = fopen($path, 'r');
        if ($handle === false) return;

        fgetcsv($handle); // skip header

        $existingCodes = Winner::pluck('unique_code')->flip()->toArray();
        $batch = [];
        $total = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $email = trim((string) ($row[0] ?? ''));
            $firstName = trim((string) ($row[1] ?? ''));
            $lastName = trim((string) ($row[2] ?? ''));

            if ($firstName === '' && $lastName === '') continue;

            do {
                $code = $codeGen->generate(10);
            } while (isset($existingCodes[$code]));
            $existingCodes[$code] = true;

            $batch[] = [
                'first_name' => $firstName,
                'last_name' => $lastName ?: 'Winner',
                'email' => $email ?: null,
                'prize_amount' => $prizeAmount,
                'prize_description' => '$5,500,000.00 Mega Cash Prize',
                'unique_code' => $code,
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

        $this->command?->info("Imported {$total} winners from {$label}.");
    }
}
