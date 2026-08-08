<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Models\Mark;
use App\Models\GradeRule;
use Illuminate\Support\Facades\DB;

class GradebookController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacherProfile;
        $exams = Exam::latest()->get();

        $allocations = $teacher->teacherSubjects()->with(['subject.schoolClass', 'section'])->get();

        $selectedExamId = $request->exam_id ?? $exams->first()?->id;
        $selectedSubjectId = $request->subject_id ?? $allocations->first()?->subject_id;
        $selectedSectionId = $request->section_id ?? $allocations->first()?->section_id;

        $students = [];
        $existingMarks = [];
        $gradeRules = GradeRule::orderByDesc('min_percentage')->get();
        $currentSubject = null;

        if ($selectedExamId && $selectedSubjectId && $selectedSectionId) {
            $currentSubject = Subject::find($selectedSubjectId);
            $students = StudentProfile::with('user')
                ->where('section_id', $selectedSectionId)
                ->orderBy('roll_number')
                ->get();

            $existingMarks = Mark::where('exam_id', $selectedExamId)
                ->where('subject_id', $selectedSubjectId)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.gradebook.index', compact(
            'exams',
            'allocations',
            'selectedExamId',
            'selectedSubjectId',
            'selectedSectionId',
            'students',
            'existingMarks',
            'gradeRules',
            'currentSubject'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'marks' => ['required', 'array'],
            'marks.*.marks_obtained' => ['required', 'numeric', 'min:0'],
            'marks.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $authId = Auth::id();

        DB::transaction(function () use ($validated, $authId) {
            foreach ($validated['marks'] as $studentId => $data) {
                Mark::updateOrCreate(
                    [
                        'exam_id' => $validated['exam_id'],
                        'subject_id' => $validated['subject_id'],
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $data['marks_obtained'],
                        'remarks' => $data['remarks'] ?? null,
                        'entered_by' => $authId,
                    ]
                );
            }
        });

        return back()->with('success', 'Exam assessment scores and grade matrix successfully saved!');
    }
}
