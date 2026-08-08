<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\StudentTransport;
use App\Models\HostelRoom;
use App\Models\StudentHostel;
use App\Models\FeeMaster;
use App\Models\FeeInvoice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentProfile::with(['user', 'schoolClass', 'section', 'parent.user']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->latest()->paginate(15);
        $classes = SchoolClass::with('sections')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::with('sections')->get();
        $parents = ParentProfile::with('user')->get();
        $routes = TransportRoute::all();
        $vehicles = Vehicle::all();
        $hostelRooms = HostelRoom::with('hostel')->get();

        return view('admin.students.create', compact('classes', 'parents', 'routes', 'vehicles', 'hostelRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Student User Info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:50'],
            
            // Academic Placement
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'admission_number' => ['required', 'string', 'unique:student_profiles,admission_number'],
            'roll_number' => ['required', 'string', 'max:50'],
            'admission_date' => ['required', 'date'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string'],

            // Parent Link
            'parent_type' => ['required', 'in:existing,new,none'],
            'parent_id' => ['nullable', 'required_if:parent_type,existing', 'exists:parent_profiles,id'],
            'parent_name' => ['nullable', 'required_if:parent_type,new', 'string', 'max:255'],
            'parent_email' => ['nullable', 'required_if:parent_type,new', 'email', 'unique:users,email'],
            'parent_phone' => ['nullable', 'string', 'max:50'],
            'parent_occupation' => ['nullable', 'string', 'max:100'],

            // Auxiliary Services
            'route_id' => ['nullable', 'exists:transport_routes,id'],
            'vehicle_id' => ['nullable', 'required_with:route_id', 'exists:vehicles,id'],
            'hostel_room_id' => ['nullable', 'exists:hostel_rooms,id'],
            'auto_generate_fee' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Create Parent Profile if new
            $parentId = null;
            if ($validated['parent_type'] === 'existing') {
                $parentId = $validated['parent_id'];
            } elseif ($validated['parent_type'] === 'new') {
                $parentUser = User::create([
                    'name' => $validated['parent_name'],
                    'email' => $validated['parent_email'],
                    'phone' => $validated['parent_phone'],
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                ]);
                $parentUser->assignRole('parent');

                $parentProf = ParentProfile::create([
                    'user_id' => $parentUser->id,
                    'occupation' => $validated['parent_occupation'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);
                $parentId = $parentProf->id;
            }

            // 2. Create Student User Account
            $studentUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);
            $studentUser->assignRole('student');

            // 3. Create Student Profile
            $studentProfile = StudentProfile::create([
                'user_id' => $studentUser->id,
                'parent_id' => $parentId,
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'roll_number' => $validated['roll_number'],
                'admission_number' => $validated['admission_number'],
                'admission_date' => $validated['admission_date'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'],
                'blood_group' => $validated['blood_group'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // 4. Transport Allocation
            if (!empty($validated['route_id']) && !empty($validated['vehicle_id'])) {
                StudentTransport::create([
                    'student_id' => $studentProfile->id,
                    'route_id' => $validated['route_id'],
                    'vehicle_id' => $validated['vehicle_id'],
                ]);
            }

            // 5. Hostel Allocation
            if (!empty($validated['hostel_room_id'])) {
                StudentHostel::create([
                    'student_id' => $studentProfile->id,
                    'room_id' => $validated['hostel_room_id'],
                ]);
            }

            // 6. Automatic Initial Fee Invoice
            if ($request->boolean('auto_generate_fee')) {
                $feeMasters = FeeMaster::where('class_id', $validated['class_id'])->get();
                $totalFee = $feeMasters->sum('amount');
                if ($totalFee > 0) {
                    FeeInvoice::create([
                        'student_id' => $studentProfile->id,
                        'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(uniqid()),
                        'title' => 'Admission & Term 1 Tuition Fee',
                        'total_amount' => $totalFee,
                        'paid_amount' => 0.00,
                        'due_date' => now()->addDays(15)->format('Y-m-d'),
                        'status' => 'unpaid',
                    ]);
                }
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Student admitted and enrolled successfully!');
    }

    public function show(StudentProfile $student)
    {
        $student->load([
            'user',
            'schoolClass',
            'section',
            'parent.user',
            'attendances' => fn($q) => $q->latest('date')->take(30),
            'marks.subject',
            'marks.exam',
            'feeInvoices.payments',
            'transport.route',
            'transport.vehicle',
            'hostel.room.hostel',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function toggleStatus(StudentProfile $student)
    {
        $user = $student->user;
        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        $statusMsg = $user->status === 'active' ? 'activated' : 'suspended';
        return back()->with('success', "Student account has been {$statusMsg}.");
    }
}
