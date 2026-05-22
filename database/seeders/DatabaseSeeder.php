<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Invitation;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = collect(['Engineering', 'Marketing', 'Sales', 'HR', 'Finance', 'Design', 'Operations', 'Others'])
            ->map(fn($name) => Department::create(['name' => $name]));

        $admin = User::create([
            'name' => 'Chukwuemeka Obene',
            'email' => 'admin@fawthritehr.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => $departments->where('name','HR')->first()->id,
            'designation' => 'HR Director',
            'phone' => '+234 800 000 0001',
            'status' => 'active',
            'date_joined' => Carbon::parse('2022-01-01'),
        ]);

        $leaveTypes = collect([
            ['name' => 'Annual Leave', 'days_entitlement' => 20, 'color' => '#6366f1'],
            ['name' => 'Sick Leave', 'days_entitlement' => 10, 'color' => '#f59e0b'],
            ['name' => 'Personal Leave', 'days_entitlement' => 5, 'color' => '#10b981'],
            ['name' => 'Maternity Leave', 'days_entitlement' => 90, 'color' => '#ec4899'],
        ])->map(fn($lt) => LeaveType::create($lt));

        $designations = ['Senior Developer', 'Product Designer', 'Backend Developer', 'UX Manager', 'Sales Executive', 'Marketing Manager', 'Finance Analyst', 'HR Manager'];
        $employees = [];
        $names = ['John Doe', 'Jane Smith', 'Michael Brown', 'Emily Davis', 'Robert Johnson', 'Sarah Wilson', 'David Lee', 'Olivia Martin'];
        foreach ($names as $i => $name) {
            $dept = $departments->values()->get($i % $departments->count());
            $emp = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@company.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department_id' => $dept->id,
                'designation' => $designations[$i % count($designations)],
                'phone' => '+234 800 000 ' . str_pad($i+2, 4, '0', STR_PAD_LEFT),
                'status' => 'active',
                'date_joined' => Carbon::now()->subDays(rand(30, 365)),
                'date_of_birth' => Carbon::now()->subYears(rand(25, 45))->subDays(rand(0,365)),
                'invited_by' => $admin->id,
                'account_setup_at' => now(),
            ]);
            $employees[] = $emp;
        }

        // Create leaves
        foreach ($employees as $i => $emp) {
            $lt = $leaveTypes->random();
            Leave::create([
                'user_id' => $emp->id,
                'leave_type_id' => $lt->id,
                'start_date' => Carbon::now()->addDays($i + 1),
                'end_date' => Carbon::now()->addDays($i + 3),
                'days' => 3,
                'reason' => 'Personal reasons',
                'status' => ['pending','approved','rejected'][$i % 3],
                'approved_by' => $i % 3 !== 0 ? $admin->id : null,
            ]);
        }

        // Create timesheets for current week
        $weekStart = Carbon::now()->startOfWeek();
        foreach ($employees as $emp) {
            for ($d = 0; $d < 5; $d++) {
                $date = $weekStart->copy()->addDays($d);
                if ($date->isWeekend() || $date->isFuture()) continue;
                $hours = rand(6, 9);
                Timesheet::create([
                    'user_id' => $emp->id,
                    'date' => $date,
                    'project_task' => ['Website Redesign', 'API Integration', 'Bug Fix & Testing', 'Mobile App Development', 'Data Analysis'][$d % 5],
                    'time_in' => '09:00:00',
                    'time_out' => '09:00:00',
                    'break_minutes' => 60,
                    'total_hours' => $hours,
                    'status' => 'submitted',
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekStart->copy()->endOfWeek()->toDateString(),
                    'submitted_at' => now(),
                ]);
            }
        }

        // Create an invitation link for demo
        Invitation::create([
            'email' => 'newemployee@company.com',
            'name' => 'New Employee',
            'token' => 'demo-invitation-token-12345',
            'invited_by' => $admin->id,
            'role' => 'employee',
            'department_id' => $departments->first()->id,
            'expires_at' => now()->addDays(7),
        ]);
    }
}
