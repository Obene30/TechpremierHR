<?php
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\InvitationSetupController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/invitation/{token}', [InvitationSetupController::class, 'show'])->name('invitation.setup');
    Route::post('/invitation/{token}', [InvitationSetupController::class, 'setup'])->name('invitation.setup.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/employees', [Admin\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{employee}', [Admin\EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employees/invite', [Admin\EmployeeController::class, 'invite'])->name('employees.invite');
    Route::patch('/employees/{employee}/status', [Admin\EmployeeController::class, 'updateStatus'])->name('employees.status');

    Route::get('/leave', [Admin\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave/{leave}/approve', [Admin\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [Admin\LeaveController::class, 'reject'])->name('leave.reject');

    Route::get('/timesheet', [Admin\TimesheetController::class, 'index'])->name('timesheet.index');
    Route::post('/timesheet/{timesheet}/approve', [Admin\TimesheetController::class, 'approve'])->name('timesheet.approve');
    Route::post('/timesheet/{timesheet}/reject', [Admin\TimesheetController::class, 'reject'])->name('timesheet.reject');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/departments', [Admin\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [Admin\DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [Admin\DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [Admin\DepartmentController::class, 'destroy'])->name('departments.destroy');
});

// Employee routes
Route::middleware('auth')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [Employee\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/leave', [Employee\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave', [Employee\LeaveController::class, 'store'])->name('leave.store');
    Route::delete('/leave/{leave}', [Employee\LeaveController::class, 'cancel'])->name('leave.cancel');

    Route::get('/timesheet', [Employee\TimesheetController::class, 'index'])->name('timesheet.index');
    Route::post('/timesheet', [Employee\TimesheetController::class, 'store'])->name('timesheet.store');
    Route::post('/timesheet/submit', [Employee\TimesheetController::class, 'submit'])->name('timesheet.submit');

    Route::get('/profile', [Employee\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [Employee\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [Employee\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/documents', [Employee\DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [Employee\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [Employee\DocumentController::class, 'destroy'])->name('documents.destroy');
});
