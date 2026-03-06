<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();
        $currentMonth = Carbon::now()->format('Y-m');

        // ── Employee stats
        $totalEmployees  = Employee::count();
        $presentToday    = Attendance::whereDate('date', $today)
                            ->whereIn('status', ['Present', 'Late'])
                            ->count();
        $absentToday     = Attendance::whereDate('date', $today)
                            ->where('status', 'Absent')
                            ->count();
        $lateToday       = Attendance::whereDate('date', $today)
                            ->where('status', 'Late')
                            ->count();
        $pendingLeaves   = Leave::where('status', 'Pending')->count();

        // ── Monthly OT / Late totals
        $monthlyOT   = Attendance::whereYear('date', Carbon::now()->year)
                        ->whereMonth('date', Carbon::now()->month)
                        ->sum('overtime_minutes');
        $monthlyLate = Attendance::whereYear('date', Carbon::now()->year)
                        ->whereMonth('date', Carbon::now()->month)
                        ->sum('late_minutes');

        // ── Recent attendance (today)
        $recentAttendance = Attendance::with('employee')
                            ->whereDate('date', $today)
                            ->latest()
                            ->take(10)
                            ->get();

        // ── Attendance trend last 7 days
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $trend[] = [
                'date'    => $d->format('M d'),
                'present' => Attendance::whereDate('date', $d->toDateString())
                                ->whereIn('status', ['Present', 'Late'])->count(),
                'absent'  => Attendance::whereDate('date', $d->toDateString())
                                ->where('status', 'Absent')->count(),
            ];
        }

        // ── Employees timed-in but not yet timed-out
        $stillIn = Attendance::with('employee')
                    ->whereDate('date', $today)
                    ->whereNotNull('time_in_am')
                    ->whereNull('time_out_final')
                    ->whereIn('status', ['Present', 'Late'])
                    ->get();

        return view('admin.dashboard', compact(
            'totalEmployees', 'presentToday', 'absentToday',
            'lateToday', 'pendingLeaves', 'monthlyOT', 'monthlyLate',
            'recentAttendance', 'trend', 'stillIn'
        ));
    }
}