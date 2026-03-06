<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;

/* ─────────────────────────────────────
   PUBLIC
───────────────────────────────────── */
Route::get('/', fn() => view('welcome'))->name('welcome');

Route::get('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/* ─────────────────────────────────────
   ADMIN  (auth + Admin role)
───────────────────────────────────── */
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Profile / password
    Route::get('/profile',           [AuthController::class, 'profileView'])->name('profile');
    Route::post('/change-password',  [AuthController::class, 'changePassword'])->name('change-password');

    // Employees CRUD
    Route::resource('employees', EmployeeController::class);

    // Attendance CRUD
    Route::get   ('attendance',            [AttendanceController::class, 'index'])  ->name('attendance.index');
    Route::get   ('attendance/create',     [AttendanceController::class, 'create']) ->name('attendance.create');
    Route::post  ('attendance',            [AttendanceController::class, 'store'])  ->name('attendance.store');
    Route::get   ('attendance/{attendance}/edit', [AttendanceController::class,'edit']) ->name('attendance.edit');
    Route::put   ('attendance/{attendance}',      [AttendanceController::class,'update']) ->name('attendance.update');
    Route::delete('attendance/{attendance}',      [AttendanceController::class,'destroy']) ->name('attendance.destroy');

    // Payroll
    Route::get   ('payroll',                  [PayrollController::class,'index'])    ->name('payroll.index');
    Route::post  ('payroll/generate',         [PayrollController::class,'generate']) ->name('payroll.generate');
    Route::get   ('payroll/export',           [PayrollController::class,'export'])   ->name('payroll.export');
    Route::get   ('payroll/{payroll}',        [PayrollController::class,'show'])     ->name('payroll.show');
    Route::get   ('payroll/{payroll}/edit',   [PayrollController::class,'edit'])     ->name('payroll.edit');
    Route::put   ('payroll/{payroll}',        [PayrollController::class,'update'])   ->name('payroll.update');
    Route::delete('payroll/{payroll}',        [PayrollController::class,'destroy'])  ->name('payroll.destroy');

    // Leaves CRUD + approve/reject
    Route::resource('leaves', LeaveController::class);
    Route::post('leaves/{leave}/approve', [LeaveController::class,'approve'])->name('leaves.approve');
    Route::post('leaves/{leave}/reject',  [LeaveController::class,'reject']) ->name('leaves.reject');
});

/* ─────────────────────────────────────
   EMPLOYEE  (auth + Employee role)
───────────────────────────────────── */
Route::middleware(['auth', 'role:Employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get ('dashboard',  [EmployeeController::class, 'dashboard'])       ->name('dashboard');
    Route::get ('attendance', [AttendanceController::class, 'employeeView'])  ->name('attendance');
    Route::post('attendance', [AttendanceController::class, 'tapTime'])       ->name('attendance.tap');
    Route::get ('leaves',     [LeaveController::class, 'employeeLeaves'])     ->name('leaves');
    Route::post('leaves',     [LeaveController::class, 'employeeStore'])      ->name('leaves.store');
    Route::post('change-password', [AuthController::class,'changePassword'])  ->name('change-password');
});