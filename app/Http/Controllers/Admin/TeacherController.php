<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = TeacherProfile::with([
            'user',
            'teacherSubjects.subject.schoolClass',
            'teacherSubjects.section',
        ])->latest()->paginate(15);

        $classes = SchoolClass::with(['sections', 'subjects'])->get();

        return view('admin.teachers.index', compact('teachers', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:50'],
            'employee_code' => ['required', 'string', 'unique:teacher_profiles,employee_code'],
            'qualification' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:100'],
            'joining_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'address' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);
            $user->assignRole('teacher');

            TeacherProfile::create([
                'user_id' => $user->id,
                'employee_code' => $validated['employee_code'],
                'qualification' => $validated['qualification'],
                'designation' => $validated['designation'],
                'joining_date' => $validated['joining_date'],
                'basic_salary' => $validated['basic_salary'],
                'address' => $validated['address'] ?? null,
            ]);
        });

        return back()->with('success', 'Faculty teacher registered successfully!');
    }

    public function assignSubject(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:teacher_profiles,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        TeacherSubject::firstOrCreate($validated);

        return back()->with('success', 'Subject and Section assigned to teacher successfully!');
    }

    public function removeSubject(TeacherSubject $teacherSubject)
    {
        $teacherSubject->delete();
        return back()->with('success', 'Subject allocation removed.');
    }
}
