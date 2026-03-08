<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\PayrollSetting;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $payrolls = Payroll::with('employee')
                    ->where('month', 'like', $month . '%')
                    ->orderBy('employee_id')
                    ->paginate(20)
                    ->withQueryString();

        $employees = Employee::orderBy('name')->get();

        // Months available
        $months = Payroll::selectRaw("DATE_FORMAT(month, '%Y-%m') as m")
                    ->distinct()->orderByDesc('m')->pluck('m');

        return view('admin.payroll.index', compact('payrolls', 'month', 'employees', 'months'));
    }

    // Generate / compute payroll for a specific month
    public function generate(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $month     = $request->month;
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Load configurable settings
        $settings    = PayrollSetting::current();
        $workingDays = $settings->working_days_per_month;
        $workingHrs  = $settings->working_hours_per_day;
        $otMultiplier = (float) $settings->ot_rate_multiplier;
        $graceMin    = $settings->late_grace_minutes;

        // Count holidays in this month
        $holidayCount = Holiday::whereBetween('date', [$startDate, $endDate])->count();
        $effectiveWorkingDays = max(1, $workingDays - $holidayCount);

        $employees = Employee::all();
        $generated = 0;

        foreach ($employees as $emp) {
            $attendances = Attendance::where('employee_id', $emp->id)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->get();

            // Count approved leave days for this employee in this month
            $approvedLeaveDays = 0;
            $approvedLeaves = Leave::where('employee_id', $emp->id)
                ->where('status', 'Approved')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                })->get();

            foreach ($approvedLeaves as $leave) {
                $leaveStart = $leave->start_date->max($startDate);
                $leaveEnd   = $leave->end_date->min($endDate);
                $approvedLeaveDays += $leaveStart->diffInDays($leaveEnd) + 1;
            }

            $daysPresent  = $attendances->whereIn('status', ['Present', 'Late'])->count();
            $rawAbsent    = $attendances->where('status', 'Absent')->count();
            $absentDays   = max(0, $rawAbsent - $approvedLeaveDays);

            // Apply grace period to late minutes
            $totalLate = $attendances->sum(function ($att) use ($graceMin) {
                return max(0, $att->late_minutes - $graceMin);
            });
            $totalOT = $attendances->sum('overtime_minutes');

            $dailyRate      = $emp->basic_salary / $effectiveWorkingDays;
            $minuteRate     = $dailyRate / ($workingHrs * 60);
            $hourRate       = $dailyRate / $workingHrs;

            $lateDeduction  = round($minuteRate * $totalLate, 2);
            $absentDeduction= round($dailyRate * $absentDays, 2);
            $overtimePay    = round($hourRate * $otMultiplier * ($totalOT / 60), 2);
            $deductions     = $lateDeduction + $absentDeduction;
            $netSalary      = round($emp->basic_salary + $overtimePay - $deductions, 2);

            Payroll::updateOrCreate(
                ['employee_id' => $emp->id, 'month' => $startDate->toDateString()],
                [
                    'total_days_present'    => $daysPresent,
                    'total_late_minutes'    => $totalLate,
                    'total_overtime_minutes'=> $totalOT,
                    'absent_days'           => $absentDays,
                    'basic_salary'          => $emp->basic_salary,
                    'overtime_pay'          => $overtimePay,
                    'deductions'            => $deductions,
                    'net_salary'            => $netSalary,
                ]
            );
            $generated++;
        }

        return redirect()->route('admin.payroll.index', ['month' => $month])
            ->with('success', "Payroll generated for {$generated} employees ({$month}). Holidays: {$holidayCount}, Working days: {$effectiveWorkingDays}.");
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee');
        $month     = Carbon::parse($payroll->month);
        $attendances = Attendance::where('employee_id', $payroll->employee_id)
                        ->whereBetween('date', [$month->startOfMonth(), $month->copy()->endOfMonth()])
                        ->orderBy('date')
                        ->get();

        return view('admin.payroll.show', compact('payroll', 'attendances'));
    }

    public function edit(Payroll $payroll)
    {
        $payroll->load('employee');
        return view('admin.payroll.edit', compact('payroll'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'basic_salary'   => 'required|numeric|min:0',
            'overtime_pay'   => 'required|numeric|min:0',
            'deductions'     => 'required|numeric|min:0',
        ]);

        $netSalary = $request->basic_salary + $request->overtime_pay - $request->deductions;

        $payroll->update([
            'basic_salary' => $request->basic_salary,
            'overtime_pay' => $request->overtime_pay,
            'deductions'   => $request->deductions,
            'net_salary'   => $netSalary,
        ]);

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll record updated.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return back()->with('success', 'Payroll record deleted.');
    }

    // Export CSV
    public function export(Request $request)
    {
        $month    = $request->month ?? Carbon::now()->format('Y-m');
        $payrolls = Payroll::with('employee')
                    ->where('month', 'like', $month . '%')
                    ->get();

        $filename = "payroll_{$month}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payrolls) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Employee ID','Name','Department','Days Present','Absent',
                'Late (min)','OT (min)','Basic Salary','OT Pay','Deductions','Net Salary'
            ]);
            foreach ($payrolls as $p) {
                fputcsv($out, [
                    $p->employee->employee_id,
                    $p->employee->name,
                    $p->employee->department,
                    $p->total_days_present,
                    $p->absent_days,
                    $p->total_late_minutes,
                    $p->total_overtime_minutes,
                    $p->basic_salary,
                    $p->overtime_pay,
                    $p->deductions,
                    $p->net_salary,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}