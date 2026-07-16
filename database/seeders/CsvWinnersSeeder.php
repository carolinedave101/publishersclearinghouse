<?php

namespace Database\Seeders;

use App\Models\Winner;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\LazyCollection;

class CsvWinnersSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('Leads ⚡️🚀PCH .csv');

        if (!file_exists($path)) {
            $this->command?->warn("CSV file not found at: {$path}");
            return;
        }

        $codeGen = app(CodeGenerator::class);
        $winners = [];
        $now = Carbon::now();
        $prizeAmount = 5500000.00;

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command?->error('Unable to open CSV file.');
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return;
        }

        $firstNameIndex = null;
        $lastNameIndex = null;
        foreach ($header as $i => $col) {
            $col = strtolower(trim(str_replace("\u{feff}", '', $col)));
            if ($col === 'fname' || $col === 'first_name' || $col === 'first name') {
                $firstNameIndex = $i;
            }
            if ($col === 'lname' || $col === 'last_name' || $col === 'last name') {
                $lastNameIndex = $i;
            }
        }

        if ($firstNameIndex === null) {
            $firstNameIndex = 0;
            $lastNameIndex = 1;
        }

        $existingCodes = Winner::pluck('unique_code')->flip()->toArray();

        while (($row = fgetcsv($handle)) !== false) {
            $firstName = trim((string) ($row[$firstNameIndex] ?? ''));
            $lastName = $lastNameIndex !== null ? trim((string) ($row[$lastNameIndex] ?? '')) : '';

            if ($firstName === '' && $lastName === '') {
                continue;
            }

            do {
                $code = $codeGen->generate(10);
            } while (isset($existingCodes[$code]));
            $existingCodes[$code] = true;

            $winners[] = [
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

            if (count($winners) >= 500) {
                Winner::insert($winners);
                $winners = [];
            }
        }

        if (!empty($winners)) {
            Winner::insert($winners);
        }

        fclose($handle);

        $this->command?->info('Imported winners from CSV. Total winners: ' . Winner::count());
    }
}
