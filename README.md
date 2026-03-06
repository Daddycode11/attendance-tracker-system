<p align="center">
  <img src="https://img.shields.io/badge/AttendanceIQ-Tracker-e85d26?style=for-the-badge&logo=clockify&logoColor=white" alt="AttendanceIQ">
</p>

<h1 align="center">AttendanceIQ — Attendance Tracker System</h1>

<p align="center">
  A modern, responsive employee attendance tracking system built with <strong>Laravel 12</strong> and <strong>MySQL</strong>.<br>
  Manage employees, track time-in/out, handle leave requests, and process payroll — all from a clean dashboard.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="MIT License">
</p>

---

## Features

### Authentication & Roles
- Username-based login (no email required)
- Role-based access control — **Admin** and **Employee** portals
- Middleware-protected routes per role
- Password change functionality

### Admin Dashboard
- Overview statistics: total employees, present today, on leave, pending requests
- Live clock with real-time updates
- Quick-access navigation sidebar
- Fully responsive — collapsible sidebar with hamburger menu on mobile

### Employee Management (CRUD)
- Full Create / Read / Update / Delete for employee records
- **Auto-generated Employee IDs** (EMP-001, EMP-002, …)
- Search and filter by name, department, or position
- Automatic user account creation with employee
- Paginated employee listing

### Time-In / Time-Out (Attendance)
- 4-step sequential time tap: **AM In → Lunch Out → PM In → Final Out**
- Live clock display on the time-tap panel
- Visual progress dots showing current tap step
- Daily attendance records with timestamps
- Employee self-service — tap from their own dashboard
- Admin can view and manage all attendance records

### Leave Management
- Employees can submit leave requests (Sick, Vacation, Personal, Emergency, Maternity, Paternity)
- Date range selection with reason field
- Admin approval/rejection workflow
- Pending leave count badge in sidebar
- Leave history per employee

### Payroll
- Admin payroll overview and management
- Per-employee basic salary tracking
- Payroll records linked to attendance data

### Responsive Design
- Mobile-first responsive layouts for both Admin and Employee portals
- Collapsible sidebar with dark overlay on mobile
- Topbar with hamburger toggle (< 768px)
- Adaptive stat grids, tables, and form layouts
- Touch-friendly buttons and controls

### UI / UX
- Clean, modern design with **Syne** + **DM Sans** typography
- Dark sidebar with accent-colored active states
- Card-based dashboard with shadow and rounded corners
- Badge system for statuses (Present, Late, Absent, Pending, Approved, Rejected)
- Auto-dismissing flash alerts
- Font Awesome 6 icons throughout

---

## Tech Stack

| Layer       | Technology                  |
|-------------|-----------------------------|
| Backend     | PHP 8.2, Laravel 12         |
| Database    | MySQL 8.0                   |
| Frontend    | Blade Templates, Vanilla CSS/JS |
| Auth        | Laravel built-in (session-based) |
| Icons       | Font Awesome 6              |
| Fonts       | Google Fonts (Syne, DM Sans)|

---

## Installation

```bash
# Clone the repository
git clone https://github.com/Daddycode11/attendance-tracker-system.git
cd attendance-tracker-system

# Install dependencies
composer install

# Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# Configure your database in .env
# DB_DATABASE=employees
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seed
php artisan migrate
php artisan db:seed --class=AdminSeeder

# Start the development server
php artisan serve
```

## Default Accounts

| Role     | Username       | Password       |
|----------|----------------|----------------|
| Admin    | admin          | Admin@1234     |
| Employee | juan.cruz      | Employee@1234  |
| Employee | maria.santos   | Employee@1234  |
| Employee | carlo.reyes    | Employee@1234  |
| Employee | ana.lim        | Employee@1234  |
| Employee | robert.garcia  | Employee@1234  |

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── AdminController.php       # Admin dashboard
│   ├── AttendanceController.php  # Attendance CRUD + time tap
│   ├── AuthController.php        # Login / logout / password
│   ├── EmployeeController.php    # Employee CRUD + employee dashboard
│   ├── LeaveController.php       # Leave requests & approval
│   └── PayrollController.php     # Payroll management
├── Models/
│   ├── Attendance.php
│   ├── Employee.php
│   ├── Leave.php
│   ├── Payroll.php
│   └── User.php
└── Middleware/
    └── RoleMiddleware.php        # Role-based route protection

resources/views/
├── layouts/
│   ├── admin.blade.php           # Admin layout (sidebar + topbar)
│   └── employee.blade.php        # Employee layout (sidebar + topbar)
├── admin/
│   ├── dashboard.blade.php
│   ├── employees/ (index, create, edit, show)
│   ├── attendance/ (index, create, edit)
│   ├── leaves/index.blade.php
│   └── payroll/index.blade.php
├── employee/
│   ├── dashboard.blade.php       # Time tap panel
│   ├── attendance.blade.php      # Attendance history
│   └── leaves.blade.php          # Leave requests
└── auth/login.blade.php
```

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
