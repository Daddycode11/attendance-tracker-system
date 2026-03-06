<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create admin employee record ───────────────────────
        $employee = Employee::firstOrCreate(
            ['employee_id' => 'ADMIN-001'],
            [
                'name'         => 'System Administrator',
                'department'   => 'Administration',
                'position'     => 'System Administrator',
                'basic_salary' => 50000.00,
            ]
        );

        // ── Create admin user account ──────────────────────────
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'employee_id' => $employee->id,
                'password'    => 'Admin@1234',
                'role'        => 'Admin',
            ]
        );

        $this->command->info('');
        $this->command->info('  ✅ Admin account ready!');
        $this->command->info('     Username : admin');
        $this->command->info('     Email    : admin@company.com');
        $this->command->info('     Password : Admin@1234');
        $this->command->warn('     ⚠  Change password after first login.');
        $this->command->info('');

        // ── Seed sample employees ──────────────────────────────
        $samples = [
            ['EMP-001', 'Juan Dela Cruz',  'Engineering', 'Software Engineer',   28000],
            ['EMP-002', 'Maria Santos',    'Engineering', 'Frontend Developer',  26000],
            ['EMP-003', 'Carlo Reyes',     'HR',          'HR Manager',          32000],
            ['EMP-004', 'Ana Lim',         'Finance',     'Accountant',          30000],
            ['EMP-005', 'Robert Garcia',   'Marketing',   'Marketing Lead',      27000],
        ];

        foreach ($samples as [$eid, $fullName, $dept, $pos, $salary]) {

            $emp = Employee::firstOrCreate(
                ['employee_id' => $eid],
                [
                    'name'         => $fullName,
                    'department'   => $dept,
                    'position'     => $pos,
                    'basic_salary' => $salary,
                ]
            );

            // Build username and email from first and last name
            $nameParts = explode(' ', strtolower($fullName));
            $username  = $nameParts[0] . '.' . end($nameParts); // e.g., juan.delacruz
            $email     = $username . '@company.com';

            User::firstOrCreate(
                ['username' => $username],
                [
                    'employee_id' => $emp->id,
                    'password'    => 'Employee@1234',
                    'role'        => 'Employee',
                ]
            );
        }

        $this->command->info('  ✅ Sample employees created!');
        $this->command->info('     Username & Email for all: first.last@company.com');
        $this->command->info('     Password for all: Employee@1234');
        $this->command->info('');
        $this->command->table(
            ['Username', 'Name', 'Department'],
            collect($samples)->map(fn($s) => [
                strtolower(explode(' ', $s[1])[0] . '.' . explode(' ', $s[1])[count(explode(' ', $s[1])) - 1]),
                $s[1],
                $s[2],
            ])->toArray()
        );
    }
}