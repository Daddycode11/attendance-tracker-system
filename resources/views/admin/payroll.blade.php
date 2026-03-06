@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payroll Management</h1>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Employee</th>
                <th>Days Present</th>
                <th>Absent Days</th>
                <th>Total Late (min)</th>
                <th>Total Overtime (min)</th>
                <th>Basic Salary</th>
                <th>Late Deduction</th>
                <th>Overtime Pay</th>
                <th>Absent Deduction</th>
                <th>Net Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $payroll)
                @php
                    $lateDeduction = ($payroll->basic_salary / 22 / 480) * $payroll->total_late_minutes;
                    $overtimePay = ($payroll->basic_salary / 22 / 8) * ($payroll->total_overtime_minutes / 60);
                    $absentDeduction = ($payroll->basic_salary / 22) * $payroll->absent_days;
                    $netSalary = $payroll->basic_salary + $overtimePay - ($lateDeduction + $absentDeduction);
                @endphp
                <tr>
                    <td>{{ $payroll->employee->name }}</td>
                    <td>{{ $payroll->total_days_present }}</td>
                    <td>{{ $payroll->absent_days }}</td>
                    <td>{{ $payroll->total_late_minutes }}</td>
                    <td>{{ $payroll->total_overtime_minutes }}</td>
                    <td>{{ number_format($payroll->basic_salary,2) }}</td>
                    <td>{{ number_format($lateDeduction,2) }}</td>
                    <td>{{ number_format($overtimePay,2) }}</td>
                    <td>{{ number_format($absentDeduction,2) }}</td>
                    <td>{{ number_format($netSalary,2) }}</td>
                    <td>
                        <a href="{{ route('payroll.show', $payroll->employee_id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('payroll.export', $payroll->employee_id) }}" class="btn btn-success btn-sm">Export CSV</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection