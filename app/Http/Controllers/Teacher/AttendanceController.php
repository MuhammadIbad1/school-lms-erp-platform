<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacherProfile;
        $allocations = $teacher->teacherSubjects()->with(['section.schoolClass'])->get();
        $sections = $allocations->pluck('section')->unique('id');

        $selectedSectionId = $request->section_id ?? $sections->first()?->id;
        $selectedDate = $request->date ?? Carbon::today()->format('Y-m-d');

        $students = [];
        $existingAttendances = [];

        if ($selectedSectionId) {
            $students = StudentProfile::with('user')
                ->where('section_id', $selectedSectionId)
                ->orderBy('roll_number')
                ->get();

            $existingAttendances = Attendance::where('section_id', $selectedSectionId)
                ->where('date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.index', compact(
            'sections',
            'selectedSectionId',
            'selectedDate',
            'students',
            'existingAttendances'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $academicYear = AcademicYear::current();
        $authId = Auth::id();

        DB::transaction(function () use ($validated, $academicYear, $authId) {
            foreach ($validated['attendance'] as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $validated['date'],
                    ],
                    [
                        'section_id' => $validated['section_id'],
                        'academic_year_id' => $academicYear->id,
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                        'marked_by' => $authId,
                    ]
                );
            }
        });

        return back()->with('success', 'Daily attendance ledger successfully updated and stored!');
    }
}
