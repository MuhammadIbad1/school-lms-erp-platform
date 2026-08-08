<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\AssignmentSubmission;
use App\Models\Notice;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Carbon\Carbon;

use App\Models\Payroll;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacherUser = Auth::user();
        $teacher = $teacherUser->teacherProfile;

        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }

        $today = Carbon::today()->format('Y-m-d');
        $dayOfWeek = Carbon::today()->format('l');

        // Allocated classes and subjects
        $allocations = $teacher->teacherSubjects()->with(['subject.schoolClass', 'section'])->get();

        // Today's Timetable
        $todaySchedule = Timetable::with(['section.schoolClass', 'subject', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('time_slot_id')
            ->get();

        // Pending Submissions to Grade
        $pendingSubmissionsCount = AssignmentSubmission::whereHas('assignment', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->where('status', 'submitted')->count();

        // Total Students in Assigned Sections
        $sectionIds = $allocations->pluck('section_id')->unique();
        $totalStudents = \App\Models\StudentProfile::whereIn('section_id', $sectionIds)->count();

        // Notices for Teachers
        $notices = Notice::whereIn('target_role', ['all', 'teacher'])->latest()->take(4)->get();

        // Recent Payslips
        $recentPayrolls = Payroll::where('teacher_id', $teacher->id)->latest()->take(5)->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'allocations',
            'todaySchedule',
            'pendingSubmissionsCount',
            'totalStudents',
            'notices',
            'recentPayrolls'
        ));
    }

    public function showPayslip(Payroll $payroll)
    {
        $teacherUser = Auth::user();
        $teacher = $teacherUser->teacherProfile;

        if (!$teacher || $payroll->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to payslip.');
        }

        $payroll->load(['teacher.user']);
        return view('admin.auxiliaries.payslip', compact('payroll'));
    }
}
