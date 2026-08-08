<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\TimeSlot;
use App\Models\StudyMaterial;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\GradeRule;

class StudentAcademicsController extends Controller
{
    public function timetable()
    {
        $student = Auth::user()->studentProfile;
        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $timetables = Timetable::with(['subject', 'teacher.user', 'timeSlot'])
            ->where('section_id', $student->section_id)
            ->get();

        $scheduleGrid = [];
        foreach ($timetables as $t) {
            $scheduleGrid[$t->day_of_week][$t->time_slot_id] = $t;
        }

        return view('student.academics.timetable', compact('student', 'timeSlots', 'days', 'scheduleGrid'));
    }

    public function studyMaterials()
    {
        $student = Auth::user()->studentProfile;
        $materials = StudyMaterial::with(['subject', 'teacher.user'])
            ->where('section_id', $student->section_id)
            ->latest()
            ->get();

        return view('student.academics.materials', compact('materials'));
    }

    public function attendance()
    {
        $student = Auth::user()->studentProfile;
        $attendances = Attendance::where('student_id', $student->id)
            ->latest('date')
            ->paginate(30);

        $presentCount = Attendance::where('student_id', $student->id)->whereIn('status', ['present', 'late'])->count();
        $totalCount = Attendance::where('student_id', $student->id)->count();
        $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 100;

        return view('student.academics.attendance', compact('student', 'attendances', 'percentage', 'presentCount', 'totalCount'));
    }

    public function reportCard(Request $request)
    {
        $student = Auth::user()->studentProfile;
        $exams = Exam::where('is_published', true)->latest()->get();
        $selectedExamId = $request->exam_id ?? $exams->first()?->id;

        $marks = [];
        $selectedExam = null;
        $totalObtained = 0;
        $totalMax = 0;
        $overallPercentage = 0;
        $finalGrade = null;

        if ($selectedExamId) {
            $selectedExam = Exam::find($selectedExamId);
            $marks = Mark::with('subject')
                ->where('student_id', $student->id)
                ->where('exam_id', $selectedExamId)
                ->get();

            foreach ($marks as $m) {
                $totalObtained += $m->marks_obtained;
                $totalMax += $m->subject?->max_marks ?? 100;
            }

            if ($totalMax > 0) {
                $overallPercentage = round(($totalObtained / $totalMax) * 100, 2);
                $finalGrade = GradeRule::getGradeForScore($overallPercentage);
            }
        }

        return view('student.academics.report_card', compact(
            'student',
            'exams',
            'selectedExamId',
            'selectedExam',
            'marks',
            'totalObtained',
            'totalMax',
            'overallPercentage',
            'finalGrade'
        ));
    }
}
