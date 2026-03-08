<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; color: #1a1a2e; margin: 0; padding: 0; }
        .wrapper { max-width: 640px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #0d1117; padding: 24px 28px; }
        .header h1 { margin: 0; color: #fff; font-size: 18px; font-weight: 700; }
        .header p { margin: 4px 0 0; color: rgba(255,255,255,.5); font-size: 13px; }
        .body { padding: 24px 28px; }
        .info { font-size: 14px; color: #555; margin-bottom: 18px; }
        .info strong { color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f8f7f4; text-align: left; padding: 10px 12px; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #eee; }
        td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .Present { background: #dcfce7; color: #166534; }
        .Late { background: #fef3c7; color: #92400e; }
        .Absent { background: #fee2e2; color: #991b1b; }
        .Half-day { background: #e0e7ff; color: #3730a3; }
        .Incomplete { background: #f3f4f6; color: #6b7280; }
        .footer { padding: 18px 28px; font-size: 12px; color: #999; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Attendance Report</h1>
            <p>AttendanceIQ System</p>
        </div>
        <div class="body">
            <div class="info">
                <strong>Employee:</strong> {{ $employeeName }}<br>
                <strong>Employee ID:</strong> {{ $employeeId }}<br>
                <strong>Generated:</strong> {{ now()->format('F j, Y h:i A') }}
            </div>

            @if($records->count())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>In (AM)</th>
                        <th>Out (Lunch)</th>
                        <th>In (PM)</th>
                        <th>Out</th>
                        <th>Late</th>
                        <th>OT</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $att)
                    <tr>
                        <td style="white-space:nowrap;">{{ $att->date->format('M d, Y') }}</td>
                        <td>{{ $att->time_in_am ? \Carbon\Carbon::parse($att->time_in_am)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_lunch ? \Carbon\Carbon::parse($att->time_out_lunch)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_in_pm ? \Carbon\Carbon::parse($att->time_in_pm)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_final ? \Carbon\Carbon::parse($att->time_out_final)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->late_minutes > 0 ? $att->late_minutes . 'm' : '—' }}</td>
                        <td>{{ $att->overtime_minutes > 0 ? $att->overtime_minutes . 'm' : '—' }}</td>
                        <td><span class="badge {{ $att->status }}">{{ $att->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="text-align:center;color:#999;padding:20px 0;">No attendance records found.</p>
            @endif
        </div>
        <div class="footer">
            This is an automated report from AttendanceIQ. Please do not reply to this email.
        </div>
    </div>
</body>
</html>
