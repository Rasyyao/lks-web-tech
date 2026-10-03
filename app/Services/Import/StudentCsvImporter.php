<?php

namespace App\Services\Import;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentCsvImporter
{
    /**
     * Import students from CSV file.
     *
     * @param string $csvPath Full path to CSV file
     * @param int|null $defaultCohortId Optional cohort ID to assign
     * @return array{
     *     created: int,
     *     skipped: int,
     *     failed: array<array{row: int, reason: string}>,
     *     credentials: array<array{username: string, name: string, temp_password: string}>
     * }
     */
    public function import(string $csvPath, ?int $defaultCohortId = null): array
    {
        $created = 0;
        $skipped = 0;
        $failed = [];
        $credentials = [];

        if (! file_exists($csvPath) || ! ($handle = fopen($csvPath, 'r'))) {
            return [
                'created' => 0,
                'skipped' => 0,
                'failed' => [['row' => 0, 'reason' => 'Berkas CSV tidak dapat dibaca.']],
                'credentials' => [],
            ];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return [
                'created' => 0,
                'skipped' => 0,
                'failed' => [['row' => 1, 'reason' => 'Berkas CSV kosong.']],
                'credentials' => [],
            ];
        }

        // Clean headers: lowercase and trim
        $header = array_map(fn ($col) => strtolower(trim((string) $col)), $header);
        $nameIdx = array_search('name', $header, true);
        $usernameIdx = array_search('username', $header, true);
        $classIdx = array_search('class', $header, true);
        $emailIdx = array_search('email', $header, true);

        if ($nameIdx === false || $usernameIdx === false) {
            fclose($handle);
            return [
                'created' => 0,
                'skipped' => 0,
                'failed' => [['row' => 1, 'reason' => 'Kolom "name" dan "username" wajib ada di baris judul CSV.']],
                'credentials' => [],
            ];
        }

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $name = trim($row[$nameIdx] ?? '');
            $username = trim($row[$usernameIdx] ?? '');
            $className = $classIdx !== false ? trim($row[$classIdx] ?? '') : '';
            $email = $emailIdx !== false ? trim($row[$emailIdx] ?? '') : null;
            if ($email === '') {
                $email = null;
            }

            if ($name === '' || $username === '') {
                $failed[] = [
                    'row' => $rowNum,
                    'reason' => 'Nama atau username kosong.',
                ];
                continue;
            }

            // Check duplicate username
            if (User::where('username', $username)->exists()) {
                $skipped++;
                continue;
            }

            // Check duplicate email if provided
            if ($email !== null && User::where('email', $email)->exists()) {
                $failed[] = [
                    'row' => $rowNum,
                    'reason' => "Email {$email} sudah digunakan.",
                ];
                continue;
            }

            $tempPassword = Str::random(10);

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($tempPassword),
                'is_active' => true,
                'must_change_password' => true,
            ]);

            $user->assignRole('student');

            // Assign to cohort by class name or default cohort
            $cohortIdToAssign = $defaultCohortId;
            if ($className !== '') {
                $cohort = Cohort::firstOrCreate(
                    ['name' => $className],
                    ['type' => 'class', 'year' => (int) date('Y')]
                );
                $cohortIdToAssign = $cohort->id;
            }

            if ($cohortIdToAssign) {
                $user->cohorts()->syncWithoutDetaching([$cohortIdToAssign]);
            }

            $credentials[] = [
                'username' => $username,
                'name' => $name,
                'temp_password' => $tempPassword,
            ];

            $created++;
        }

        fclose($handle);

        return [
            'created' => $created,
            'skipped' => $skipped,
            'failed' => $failed,
            'credentials' => $credentials,
        ];
    }
}
