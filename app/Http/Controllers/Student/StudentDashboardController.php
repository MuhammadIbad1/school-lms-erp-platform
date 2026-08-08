<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Notice;
use App\Models\ExamSchedule;
use App\Models\FeeInvoice;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $studentUser = Auth::user();
        $student = $studentUser->studentProfile;

        if (!$student) {
            abort(403, 'Student profile not configured.');
        }

        $today = Carbon::today()->format('Y-m-d');
        $dayOfWeek = Carbon::today()->format('l');

        // Today's Timetable
        $todaySchedule = Timetable::with(['subject', 'teacher.user', 'timeSlot'])
            ->where('section_id', $student->section_id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('time_slot_id')
            ->get();

        // Active Assignments
        $assignments = Assignment::with('subject')
            ->where('section_id', $student->section_id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $submissions = AssignmentSubmission::where('student_id', $student->id)->get()->keyBy('assignment_id');

        // Upcoming Exam Schedules
        $upcomingExams = ExamSchedule::with(['exam', 'subject'])
            ->where('section_id', $student->section_id)
            ->where('exam_date', '>=', $today)
            ->orderBy('exam_date')
            ->take(4)
            ->get();

        // Notices for Students
        $notices = Notice::whereIn('target_role', ['all', 'student'])->latest()->take(4)->get();

        // Attendance Percentage
        $attendancePercentage = $student->attendance_percentage;

        // Pending Fees
        $unpaidFees = FeeInvoice::where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->sum('total_amount') - FeeInvoice::where('student_id', $student->id)->where('status', '!=', 'paid')->sum('paid_amount');

        return view('student.dashboard', compact(
            'student',
            'todaySchedule',
            'assignments',
            'submissions',
            'upcomingExams',
            'notices',
            'attendancePercentage',
            'unpaidFees'
        ));
    }
}
