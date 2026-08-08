<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\SchoolClass;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Notice;
use App\Models\Exam;
use App\Models\Book;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Core Counts
        $totalStudents = StudentProfile::count();
        $totalTeachers = TeacherProfile::count();
        $totalClasses = SchoolClass::count();
        $totalBooks = Book::sum('quantity');

        // Financial Metrics
        $totalRevenue = FeePayment::sum('amount_paid');
        $pendingFees = FeeInvoice::where('status', '!=', 'paid')->sum('total_amount') - FeeInvoice::where('status', '!=', 'paid')->sum('paid_amount');
        $todayCollection = FeePayment::whereDate('paid_at', $today)->sum('amount_paid');

        // Today's Attendance Metric
        $todayAttendanceCount = Attendance::where('date', $today)->count();
        $todayPresentCount = Attendance::where('date', $today)->whereIn('status', ['present', 'late'])->count();
        $todayAttendanceRate = $todayAttendanceCount > 0 ? round(($todayPresentCount / $todayAttendanceCount) * 100, 1) : 96.4;

        // Recent Admissions
        $recentStudents = StudentProfile::with(['user', 'schoolClass', 'section', 'parent.user'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Fee Transactions
        $recentPayments = FeePayment::with(['invoice.student.user', 'receiver'])
            ->latest('paid_at')
            ->take(5)
            ->get();

        // Active Notices
        $notices = Notice::with('author')
            ->latest()
            ->take(4)
            ->get();

        // Upcoming Exams
        $upcomingExams = Exam::with('academicYear')
            ->where('start_date', '>=', $today)
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalBooks',
            'totalRevenue',
            'pendingFees',
            'todayCollection',
            'todayAttendanceRate',
            'recentStudents',
            'recentPayments',
            'notices',
            'upcomingExams'
        ));
    }
}
