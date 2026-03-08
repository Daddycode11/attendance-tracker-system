<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\PayrollSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);

        // Default department
        Department::firstOrCreate(
            ['name' => 'Tourism'],
            ['description' => 'Tourism Department']
        );

        // Default payroll settings
        PayrollSetting::current();

        $this->command->info('  ✅ Default department & payroll settings seeded.');
    }
}