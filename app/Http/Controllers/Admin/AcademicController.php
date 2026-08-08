<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\DB;

class AcademicController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::with(['sections.classTeacher.user', 'subjects'])->withCount('studentProfiles')->get();
        $teachers = TeacherProfile::with('user')->get();

        return view('admin.academics.index', compact('academicYears', 'classes', 'teachers'));
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'numeric_code' => ['required', 'integer', 'unique:classes,numeric_code'],
            'sections' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($validated) {
            $class = SchoolClass::create([
                'name' => $validated['name'],
                'numeric_code' => $validated['numeric_code'],
            ]);

            $sections = $validated['sections'] ?? ['Section A'];
            foreach ($sections as $secName) {
                if (!empty(trim($secName))) {
                    Section::create([
                        'class_id' => $class->id,
                        'name' => trim($secName),
                        'capacity' => 40,
                    ]);
                }
            }
        });

        return back()->with('success', 'Class and sections created successfully!');
    }

    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'class_teacher_id' => ['nullable', 'exists:teacher_profiles,id'],
        ]);

        Section::create($validated);

        return back()->with('success', 'Section created successfully!');
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20'],
            'pass_marks' => ['required', 'integer', 'min:1', 'max:100'],
            'max_marks' => ['required', 'integer', 'min:1', 'max:500'],
            'type' => ['required', 'in:theory,practical,both'],
        ]);

        Subject::create($validated);

        return back()->with('success', 'Subject created and linked to class successfully!');
    }

    public function storeAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_current'])) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => !empty($validated['is_current']),
        ]);

        return back()->with('success', 'Academic session recorded successfully!');
    }
}
