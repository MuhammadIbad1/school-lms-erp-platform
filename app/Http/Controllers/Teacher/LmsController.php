<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyMaterial;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Storage;

class LmsController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacherProfile;
        $allocations = $teacher->teacherSubjects()->with(['subject.schoolClass', 'section'])->get();

        $materials = StudyMaterial::with(['subject', 'section'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $assignments = Assignment::with(['subject', 'section'])
            ->withCount(['submissions as total_submissions', 'submissions as graded_submissions' => fn($q) => $q->where('status', 'graded')])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return view('teacher.lms.index', compact('allocations', 'materials', 'assignments'));
    }

    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'], // 20MB max
        ]);

        $filePath = $request->file('file')->store('materials', 'public');
        $extension = $request->file('file')->getClientOriginalExtension();

        StudyMaterial::create([
            'subject_id' => $validated['subject_id'],
            'section_id' => $validated['section_id'],
            'teacher_id' => Auth::user()->teacherProfile->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_type' => $extension,
        ]);

        return back()->with('success', 'Study resource uploaded and published to classroom hub!');
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date', 'after:now'],
            'max_marks' => ['required', 'integer', 'min:1', 'max:500'],
            'attachment' => ['nullable', 'file', 'max:15360'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('assignments', 'public');
        }

        Assignment::create([
            'subject_id' => $validated['subject_id'],
            'section_id' => $validated['section_id'],
            'teacher_id' => Auth::user()->teacherProfile->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'due_date' => $validated['due_date'],
            'max_marks' => $validated['max_marks'],
        ]);

        return back()->with('success', 'Assignment published to students with deadline!');
    }

    public function showSubmissions(Assignment $assignment)
    {
        $assignment->load(['subject', 'section', 'submissions.student.user']);
        return view('teacher.lms.submissions', compact('assignment'));
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'marks_obtained' => ['required', 'numeric', 'min:0', "max:{$submission->assignment->max_marks}"],
            'feedback' => ['nullable', 'string', 'max:500'],
        ]);

        $submission->update([
            'marks_obtained' => $validated['marks_obtained'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
        ]);

        return back()->with('success', 'Student submission scored and feedback returned!');
    }
}
