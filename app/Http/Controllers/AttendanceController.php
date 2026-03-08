<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Mail\AttendanceReport;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ── ADMIN: List all attendance
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records   = $query->orderBy('date', 'desc')->orderBy('employee_id')->paginate(20)->withQueryString();
        $employees = Employee::orderBy('name')->get();
        $date      = $request->date ?? today()->toDateString();

        return view('admin.attendance.index', compact('records', 'employees', 'date'));
    }

    // ── ADMIN: Edit attendance record
    public function edit(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('admin.attendance.edit', compact('attendance'));
    }

    // ── ADMIN: Update attendance record
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'time_in_am'      => 'nullable|date_format:H:i',
            'time_out_lunch'  => 'nullable|date_format:H:i',
            'time_in_pm'      => 'nullable|date_format:H:i',
            'time_out_final'  => 'nullable|date_format:H:i',
            'status'          => 'required|in:Present,Late,Absent,Half-day,Incomplete',
        ]);

        $data = $request->only(['time_in_am','time_out_lunch','time_in_pm','time_out_final','status']);

        // Recalculate late / overtime
        if (!empty($data['time_in_am'])) {
            $official   = Carbon::createFromFormat('H:i', '08:00');
            $actual     = Carbon::createFromFormat('H:i', $data['time_in_am']);
            $data['late_minutes'] = max(0, $actual->diffInMinutes($official, false) * -1);
            if ($data['late_minutes'] > 0 && $data['status'] === 'Present') {
                $data['status'] = 'Late';
            }
        }

        if (!empty($data['time_out_final'])) {
            $officialOut = Carbon::createFromFormat('H:i', '17:00');
            $actualOut   = Carbon::createFromFormat('H:i', $data['time_out_final']);
            $data['overtime_minutes'] = max(0, $actualOut->diffInMinutes($officialOut, false));
        }

        $oldValues = $attendance->getOriginal();
        $attendance->update($data);

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id'       => auth()->id(),
            'action'        => 'updated',
            'old_values'    => $oldValues,
            'new_values'    => $attendance->fresh()->toArray(),
        ]);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record updated.');
    }

    // ── ADMIN: Delete attendance record
    public function destroy(Attendance $attendance)
    {
        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id'       => auth()->id(),
            'action'        => 'deleted',
            'old_values'    => $attendance->toArray(),
            'new_values'    => null,
        ]);

        $attendance->delete();
        return back()->with('success', 'Attendance record deleted.');
    }

    // ── ADMIN: Create manual attendance
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'date'           => 'required|date',
            'time_in_am'     => 'nullable|date_format:H:i',
            'time_out_lunch' => 'nullable|date_format:H:i',
            'time_in_pm'     => 'nullable|date_format:H:i',
            'time_out_final' => 'nullable|date_format:H:i',
            'status'         => 'required|in:Present,Late,Absent,Half-day,Incomplete',
        ]);

        $existing = Attendance::where('employee_id', $request->employee_id)
                        ->whereDate('date', $request->date)->first();

        if ($existing) {
            return back()->withErrors(['date' => 'Attendance record already exists for this date.'])->withInput();
        }

        $data = $request->only(['employee_id','date','time_in_am','time_out_lunch','time_in_pm','time_out_final','status']);

        // Calculate late / OT
        if (!empty($data['time_in_am'])) {
            $official = Carbon::createFromFormat('H:i', '08:00');
            $actual   = Carbon::createFromFormat('H:i', $data['time_in_am']);
            $data['late_minutes'] = max(0, $actual->diffInMinutes($official, false) * -1);
        }
        if (!empty($data['time_out_final'])) {
            $officialOut = Carbon::createFromFormat('H:i', '17:00');
            $actualOut   = Carbon::createFromFormat('H:i', $data['time_out_final']);
            $data['overtime_minutes'] = max(0, $actualOut->diffInMinutes($officialOut, false));
        }

        $record = Attendance::create($data);

        AttendanceLog::create([
            'attendance_id' => $record->id,
            'user_id'       => auth()->id(),
            'action'        => 'created',
            'old_values'    => null,
            'new_values'    => $record->toArray(),
        ]);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record created.');
    }

    // ── EMPLOYEE: View own attendance + Time In/Out button
    public function employeeView()
    {
        $employee   = auth()->user()->employee;
        $today      = today()->toDateString();
        $attendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $today)->first();

        $history = Attendance::where('employee_id', $employee->id)
                    ->orderByDesc('date')->take(15)->get();

        return view('employee.attendance', compact('attendance', 'history', 'employee'));
    }

    // ── EMPLOYEE: Time In / Out action
    public function tapTime(Request $request)
    {
        $employee   = auth()->user()->employee;
        $today      = today()->toDateString();
        $now        = now();

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            ['status' => 'Present']
        );

        $officialIn  = Carbon::createFromTime(8, 0, 0);
        $officialOut = Carbon::createFromTime(17, 0, 0);

        if (is_null($attendance->time_in_am)) {
            // Morning Time In
            $attendance->time_in_am  = $now->format('H:i:s');
            $late = max(0, $now->diffInMinutes($officialIn, false) * -1);
            $attendance->late_minutes = $late;
            $attendance->status       = $late > 0 ? 'Late' : 'Present';
            $msg = '✅ Morning Time In recorded: ' . $now->format('h:i A');

        } elseif (is_null($attendance->time_out_lunch)) {
            // Lunch Time Out
            $attendance->time_out_lunch = $now->format('H:i:s');
            $msg = '🍽 Lunch Time Out recorded: ' . $now->format('h:i A');

        } elseif (is_null($attendance->time_in_pm)) {
            // Afternoon Time In
            $attendance->time_in_pm = $now->format('H:i:s');
            $msg = '✅ Afternoon Time In recorded: ' . $now->format('h:i A');

        } else {
            // Final Time Out
            $attendance->time_out_final    = $now->format('H:i:s');
            $ot = max(0, $now->diffInMinutes($officialOut, false));
            $attendance->overtime_minutes  = $ot;

            // Check undertime
            if ($now->lt($officialOut)) {
                $attendance->undertime_minutes = $now->diffInMinutes($officialOut);
                $attendance->status = 'Incomplete';
            }
            $msg = '🏁 Final Time Out recorded: ' . $now->format('h:i A') . ($ot > 0 ? " (OT: {$ot} min)" : '');
        }

        $attendance->save();

        return back()->with('tap_success', $msg);
    }

    // ── EMPLOYEE: Email attendance report
    public function emailAttendance(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $employee = auth()->user()->employee;
        $records  = Attendance::where('employee_id', $employee->id)
                        ->orderByDesc('date')->take(15)->get();

        Mail::to($request->email)->send(
            new AttendanceReport($employee->name, $employee->employee_id, $records)
        );

        return back()->with('success', 'Attendance report sent to ' . $request->email);
    }
}