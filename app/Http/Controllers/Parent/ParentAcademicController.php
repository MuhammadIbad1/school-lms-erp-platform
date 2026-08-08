<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentProfile;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\GradeRule;
use App\Models\Timetable;
use App\Models\TimeSlot;

class ParentAcademicController extends Controller
{
    public function attendance(Request $request)
    {
        $parent = Auth::user()->parentProfile;
        $child = $this->getSelectedChild($request);

        $attendances = Attendance::where('student_id', $child->id)
            ->latest('date')
            ->paginate(30);

        $presentCount = Attendance::where('student_id', $child->id)->whereIn('status', ['present', 'late'])->count();
        $totalCount = Attendance::where('student_id', $child->id)->count();
        $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 100;

        return view('parent.academics.attendance', compact('child', 'attendances', 'percentage', 'presentCount', 'totalCount'));
    }

    public function reportCard(Request $request)
    {
        $child = $this->getSelectedChild($request);
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
                ->where('student_id', $child->id)
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

        return view('parent.academics.report_card', compact(
            'child',
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

    public function timetable(Request $request)
    {
        $child = $this->getSelectedChild($request);
        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $timetables = Timetable::with(['subject', 'teacher.user', 'timeSlot'])
            ->where('section_id', $child->section_id)
            ->get();

        $scheduleGrid = [];
        foreach ($timetables as $t) {
            $scheduleGrid[$t->day_of_week][$t->time_slot_id] = $t;
        }

        return view('parent.academics.timetable', compact('child', 'timeSlots', 'days', 'scheduleGrid'));
    }

    protected function getSelectedChild(Request $request): StudentProfile
    {
        $parent = Auth::user()->parentProfile;
        $childId = $request->child_id ?? session('selected_child_id');
        $child = $parent->students()->with(['user', 'schoolClass', 'section'])->where('id', $childId)->first();

        if (!$child) {
            $child = $parent->students()->with(['user', 'schoolClass', 'section'])->firstOrFail();
            session(['selected_child_id' => $child->id]);
        }

        return $child;
    }
}
