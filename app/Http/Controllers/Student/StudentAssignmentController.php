<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;

class StudentAssignmentController extends Controller
{
    public function index()
    {
        $student = Auth::user()->studentProfile;

        $assignments = Assignment::with(['subject', 'teacher.user'])
            ->where('section_id', $student->section_id)
            ->latest()
            ->get();

        $submissions = AssignmentSubmission::where('student_id', $student->id)->get()->keyBy('assignment_id');

        return view('student.assignments.index', compact('assignments', 'submissions'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $student = Auth::user()->studentProfile;

        $validated = $request->validate([
            'submission_file' => ['required', 'file', 'max:25600'], // 25MB max
        ]);

        $filePath = $request->file('submission_file')->store('submissions', 'public');
        $isLate = now()->isAfter($assignment->due_date);

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $filePath,
                'submitted_at' => now(),
                'status' => $isLate ? 'late' : 'submitted',
            ]
        );

        return back()->with('success', 'Your assignment has been submitted successfully to the faculty teacher!');
    }
}
