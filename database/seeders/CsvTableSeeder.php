<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CsvTableSeeder extends Seeder
{
    /**
     * Seed a table from a CSV file.
     * 
     * @param string $table The table name
     * @param string $filename The CSV filename in database/data/
     * @param string $delimiter CSV delimiter
     */
    public function seedFromCsv(string $table, string $filename, string $delimiter = ',')
    {
        $path = database_path('data/' . $filename);
        
        if (!File::exists($path)) {
            $this->command->warn("File not found: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file, 0, $delimiter);
        
        if (!$header) {
            $this->command->error("Empty or invalid CSV: $filename");
            fclose($file);
            return;
        }

        $count = 0;
        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row, fn($value) => $value !== null && $value !== ''))) {
                continue;
            }

            $data = array_combine($header, $row);
            
            // Clean up values
            foreach ($data as $key => $value) {
                if ($value === '1' || strtolower($value) === 'true') $data[$key] = true;
                elseif ($value === '0' || strtolower($value) === 'false') $data[$key] = false;
                elseif ($value === '') $data[$key] = null;
            }

            // Determine if timestamps are needed
            $insertData = $data;
            if (isset($insertData['password']) && !empty($insertData['password'])) {
                $insertData['password'] = bcrypt($insertData['password']);
            }
            if (!isset($data['created_at'])) {
                $insertData['created_at'] = now();
            }
            if (!isset($data['updated_at'])) {
                $insertData['updated_at'] = now();
            }

            DB::table($table)->insert($insertData);
            $count++;
        }

        fclose($file);
        $this->command->info("Seeded $count records into $table from $filename");
    }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Seeding order matters due to foreign key constraints
        $this->seedFromCsv('directorates', 'directorates.csv');
        $this->seedFromCsv('disability_types', 'disability_types.csv');
        $this->seedFromCsv('disease_types', 'disease_types.csv');
        $this->seedFromCsv('employment_statuses', 'employment_statuses.csv');
        $this->seedFromCsv('provinces', 'provinces.csv');
        $this->seedFromCsv('teams', 'teams.csv');
        $this->seedFromCsv('departments', 'departments.csv'); // Depends on directorates
        $this->seedFromCsv('districts', 'districts.csv'); // Depends on provinces
        $this->seedFromCsv('townships', 'townships.csv'); // Depends on districts
        $this->seedFromCsv('positions', 'positions.csv'); // Depends on departments
        $this->seedFromCsv('incident_types', 'incident_types.csv');
        $this->seedFromCsv('schedules', 'schedules.csv');
        
        // Identity & Access
        $this->seedFromCsv('permissions', 'permissions.csv');
        $this->seedFromCsv('roles', 'roles.csv');
        $this->seedFromCsv('users', 'users.csv');
        $this->seedFromCsv('employees', 'employees.csv');

        Schema::enableForeignKeyConstraints();
    }
}
