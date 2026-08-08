<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\AuxiliaryController;
use App\Http\Controllers\Admin\NoticeController;

// Teacher Controllers
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\GradebookController;
use App\Http\Controllers\Teacher\LmsController;

// Student Controllers
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentAcademicsController;
use App\Http\Controllers\Student\StudentAssignmentController;
use App\Http\Controllers\Student\StudentFeeController;

// Parent Controllers
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentAcademicController;
use App\Http\Controllers\Parent\ParentFeeController;

/*
|--------------------------------------------------------------------------
| Authentication & Root Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 1. SUPER ADMIN / PRINCIPAL PORTAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:super-admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Academic Setup
    Route::get('/academics', [AcademicController::class, 'index'])->name('academics.index');
    Route::post('/academics/class', [AcademicController::class, 'storeClass'])->name('academics.class.store');
    Route::post('/academics/section', [AcademicController::class, 'storeSection'])->name('academics.section.store');
    Route::post('/academics/subject', [AcademicController::class, 'storeSubject'])->name('academics.subject.store');
    Route::post('/academics/year', [AcademicController::class, 'storeAcademicYear'])->name('academics.year.store');

    // Student Registry & Admissions
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::patch('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');

    // Faculty & Subject Allocation
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::post('/teachers/assign-subject', [TeacherController::class, 'assignSubject'])->name('teachers.assign-subject');
    Route::delete('/teachers/remove-subject/{teacherSubject}', [TeacherController::class, 'removeSubject'])->name('teachers.remove-subject');

    // RBAC & Permission Matrix
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'syncPermissions'])->name('roles.permissions');
    Route::post('/users/{user}/assign-role', [RolePermissionController::class, 'assignUserRole'])->name('roles.assign-user');

    // Finance & Fee Management
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees/groups', [FeeController::class, 'storeGroup'])->name('fees.groups.store');
    Route::post('/fees/masters', [FeeController::class, 'storeMaster'])->name('fees.masters.store');
    Route::post('/fees/generate-batch', [FeeController::class, 'generateBatch'])->name('fees.generate-batch');
    Route::post('/fees/record-payment', [FeeController::class, 'recordPayment'])->name('fees.record-payment');
    Route::post('/fees/{invoice}/approve-cash', [FeeController::class, 'approveCashPayment'])->name('fees.approve-cash');
    Route::post('/fees/{invoice}/reject-cash', [FeeController::class, 'rejectPaymentRequest'])->name('fees.reject-cash');
    Route::get('/fees/receipt/{invoice}', [FeeController::class, 'showReceipt'])->name('fees.receipt');

    // Auxiliaries Subsystems
    Route::get('/library', [AuxiliaryController::class, 'library'])->name('library.index');
    Route::post('/library/books', [AuxiliaryController::class, 'storeBook'])->name('library.books.store');
    Route::post('/library/issue', [AuxiliaryController::class, 'issueBook'])->name('library.issue');
    Route::post('/library/return/{issue}', [AuxiliaryController::class, 'returnBook'])->name('library.return');

    Route::get('/transport', [AuxiliaryController::class, 'transport'])->name('transport.index');
    Route::post('/transport/routes', [AuxiliaryController::class, 'storeRoute'])->name('transport.routes.store');
    Route::post('/transport/vehicles', [AuxiliaryController::class, 'storeVehicle'])->name('transport.vehicles.store');

    Route::get('/hostel', [AuxiliaryController::class, 'hostel'])->name('hostel.index');
    Route::post('/hostel/hostels', [AuxiliaryController::class, 'storeHostel'])->name('hostel.hostels.store');
    Route::post('/hostel/rooms', [AuxiliaryController::class, 'storeHostelRoom'])->name('hostel.rooms.store');

    Route::get('/inventory', [AuxiliaryController::class, 'inventory'])->name('inventory.index');
    Route::post('/inventory/categories', [AuxiliaryController::class, 'storeCategory'])->name('inventory.categories.store');
    Route::post('/inventory/items', [AuxiliaryController::class, 'storeItem'])->name('inventory.items.store');

    Route::get('/payroll', [AuxiliaryController::class, 'payroll'])->name('payroll.index');
    Route::post('/payroll/generate', [AuxiliaryController::class, 'generatePayroll'])->name('payroll.generate');
    Route::get('/payroll/payslip/{payroll}', [AuxiliaryController::class, 'showPayslip'])->name('payroll.payslip');

    // Notices & Broadcast
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
});

/*
|--------------------------------------------------------------------------
| 2. TEACHER WORKSPACE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Attendance Matrix
    Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

    // Assessment Gradebook
    Route::get('/gradebook', [GradebookController::class, 'index'])->name('gradebook.index');
    Route::post('/gradebook', [GradebookController::class, 'store'])->name('gradebook.store');

    // LMS Materials & Assignments
    Route::get('/lms', [LmsController::class, 'index'])->name('lms.index');
    Route::post('/lms/materials', [LmsController::class, 'storeMaterial'])->name('lms.materials.store');
    Route::post('/lms/assignments', [LmsController::class, 'storeAssignment'])->name('lms.assignments.store');
    Route::get('/lms/assignments/{assignment}/submissions', [LmsController::class, 'showSubmissions'])->name('lms.assignments.submissions');
    Route::post('/lms/submissions/{submission}/grade', [LmsController::class, 'gradeSubmission'])->name('lms.submissions.grade');

    // Salary Payslip
    Route::get('/payroll/payslip/{payroll}', [TeacherDashboardController::class, 'showPayslip'])->name('payroll.payslip');
});

/*
|--------------------------------------------------------------------------
| 3. STUDENT PORTAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:student', 'student.self'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Academics & Progress
    Route::get('/timetable', [StudentAcademicsController::class, 'timetable'])->name('timetable');
    Route::get('/materials', [StudentAcademicsController::class, 'studyMaterials'])->name('materials');
    Route::get('/attendance', [StudentAcademicsController::class, 'attendance'])->name('attendance');
    Route::get('/report-card', [StudentAcademicsController::class, 'reportCard'])->name('report-card');

    // Homework & Assignments
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');

    // Fee Invoices
    Route::get('/fees', [StudentFeeController::class, 'index'])->name('fees.index');
});

/*
|--------------------------------------------------------------------------
| 4. PARENT PORTAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:parent', 'parent.child'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

    // Child Progress Tracking
    Route::get('/attendance', [ParentAcademicController::class, 'attendance'])->name('attendance');
    Route::get('/report-card', [ParentAcademicController::class, 'reportCard'])->name('report-card');
    Route::get('/timetable', [ParentAcademicController::class, 'timetable'])->name('timetable');

    // Child Invoices & Online Payments
    Route::get('/fees', [ParentFeeController::class, 'index'])->name('fees.index');
    Route::post('/fees/{invoice}/pay', [ParentFeeController::class, 'payOnline'])->name('fees.pay');
});
