<?php

namespace App\Console\Commands;

use App\Models\Winner;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportWinnersCsv extends Command
{
    protected $signature = 'winners:import-csv {file? : Path to CSV file}';
    protected $description = 'Import/enrich winners from demographic CSV';

    // Maps CSV header names → demographics JSON keys
    private array $demoFields = [
        'NETWORTH' => 'net_worth',
        'CREDITRATING' => 'credit_rating',
        'HOMEOWNERPROBABILITYMODEL' => 'homeowner_probability',
        'PERSONMARITALSTATUS' => 'marital_status',
        'PRESENCEOFCREDITCARD' => 'has_credit_card',
        'CREDITCARDUSER' => 'credit_card_user',
        'NUMBEROFPERSONSINLIVINGUNIT' => 'household_size',
        'GAMINGCASINO' => 'gaming_casino',
        'SWEEPSTAKES' => 'sweepstakes',
        'OCCUPATIONGROUP' => 'occupation_group',
        'ESTIMATEDINCOMECODE' => 'estimated_income_code',
        'INVESTMENT' => 'has_investments',
        'INVESTMENTSTOCKSECURITIES' => 'has_stocks_securities',
        'INVESTING_ACTIVE' => 'investing_active',
    ];

    public function handle(CodeGenerator $codeGen): int
    {
        $path = $this->argument('file') ?: base_path('25-8-IT-A-GUH-HARDFI-DEM-KEEP-UP-part15 (1).csv');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error('Failed to open CSV.');
            return self::FAILURE;
        }

        $rawHeader = fgetcsv($handle);
        if ($rawHeader === false) {
            fclose($handle);
            $this->error('Empty CSV.');
            return self::FAILURE;
        }

        $header = array_map(fn ($c) => trim(str_replace("\u{feff}", '', $c)), $rawHeader);
        $colIdx = array_flip($header);

        $required = ['PERSONFIRSTNAME', 'PERSONLASTNAME'];
        foreach ($required as $r) {
            if (!isset($colIdx[$r])) {
                $this->error("Required column '{$r}' not found.");
                $this->line('Header: ' . implode(', ', $header));
                fclose($handle);
                return self::FAILURE;
            }
        }

        $now = Carbon::now();
        $existingCodes = Winner::pluck('unique_code')->flip()->toArray();

        $updated = 0;
        $inserted = 0;
        $skipped = 0;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            $firstName = $this->titleCase($this->csv($row, $colIdx, 'PERSONFIRSTNAME'));
            $lastName = $this->titleCase($this->csv($row, $colIdx, 'PERSONLASTNAME'));

            if ($firstName === '' && $lastName === '') {
                $skipped++;
                continue;
            }

            $email = $this->cleanEmail($this->csv($row, $colIdx, 'EMAIL'));
            $phone = $this->cleanPhone($this->csv($row, $colIdx, 'PHONE'));
            $address = $this->csv($row, $colIdx, 'PRIMARYADDRESS');
            $city = $this->titleCase($this->csv($row, $colIdx, 'CITYNAME'));
            $state = strtoupper($this->csv($row, $colIdx, 'STATE'));
            $zip = $this->csv($row, $colIdx, 'ZIPCODE');
            $dob = $this->parseDob($this->csv($row, $colIdx, 'PERSONDATEOFBIRTHDATE'));
            $gender = strtoupper($this->csv($row, $colIdx, 'PERSONGENDER'));

            $demographics = [];
            foreach ($this->demoFields as $csvCol => $jsonKey) {
                $val = $this->csv($row, $colIdx, $csvCol);
                if ($val === '') continue;
                $lower = strtolower($val);
                if (in_array($lower, ['true', 'false'], true)) {
                    $demographics[$jsonKey] = $lower === 'true';
                } else {
                    $demographics[$jsonKey] = $val;
                }
            }

            $updateData = [
                'address' => $address ?: null,
                'city' => $city ?: null,
                'state' => $state ?: null,
                'zip' => $zip ?: null,
                'phone' => $phone ?: null,
                'date_of_birth' => $dob,
                'gender' => $gender ?: null,
                'demographics' => !empty($demographics) ? $demographics : null,
            ];

            // Match by email
            $winner = $email ? Winner::where('email', $email)->first() : null;

            // Fallback: match by exact name
            if (!$winner) {
                $winner = Winner::whereRaw('LOWER(first_name) = ? AND LOWER(last_name) = ?', [
                    strtolower($firstName), strtolower($lastName),
                ])->first();
            }

            if ($winner) {
                $winner->update(array_filter($updateData, fn ($v) => $v !== null));
                $updated++;
            } else {
                do {
                    $code = $codeGen->generate(10);
                } while (isset($existingCodes[$code]));
                $existingCodes[$code] = true;

                $rowData = array_merge([
                    'first_name' => $firstName ?: 'Winner',
                    'last_name' => $lastName ?: 'Winner',
                    'prize_amount' => 5500000.00,
                    'prize_description' => '$5,500,000.00 Mega Cash Prize',
                    'unique_code' => $code,
                    'email' => $email ?: null,
                    'is_claimed' => false,
                    'is_active' => true,
                    'status' => 'new',
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $updateData);

                // JSON encode demographics for raw insert
                if (isset($rowData['demographics']) && is_array($rowData['demographics'])) {
                    $rowData['demographics'] = json_encode($rowData['demographics']);
                }

                $batch[] = $rowData;
                $inserted++;

                if (count($batch) >= 200) {
                    Winner::insert($batch);
                    $batch = [];
                }
            }
        }

        fclose($handle);

        if (!empty($batch)) {
            foreach ($batch as &$row) {
                if (isset($row['demographics']) && is_array($row['demographics'])) {
                    $row['demographics'] = json_encode($row['demographics']);
                }
            }
            unset($row);
            Winner::insert($batch);
        }

        $this->table(
            ['Result', 'Count'],
            [
                ['Updated (matched existing)', $updated],
                ['Inserted (new winners)', $inserted],
                ['Skipped (empty rows)', $skipped],
            ]
        );

        $this->info("Total winners now: " . Winner::count());

        return self::SUCCESS;
    }

    private function csv(array $row, array $colIdx, string $col): string
    {
        $i = $colIdx[$col] ?? null;
        if ($i === null) return '';
        return trim(str_replace('"', '', (string) ($row[$i] ?? '')));
    }

    private function titleCase(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    private function cleanEmail(string $raw): string
    {
        return filter_var($raw, FILTER_VALIDATE_EMAIL) ? strtolower($raw) : '';
    }

    private function cleanPhone(string $raw): string
    {
        $phone = preg_replace('/[^0-9]/', '', $raw);
        if (strlen($phone) === 11 && $phone[0] === '1') {
            $phone = substr($phone, 1);
        }
        return strlen($phone) >= 10 ? $phone : '';
    }

    private function parseDob(string $raw): ?string
    {
        if ($raw === '') return null;
        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
