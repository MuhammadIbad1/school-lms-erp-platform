<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ParentProfile;
use App\Models\TeacherProfile;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentProfile;
use App\Models\TeacherSubject;
use App\Models\TimeSlot;
use App\Models\Timetable;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Mark;
use App\Models\GradeRule;
use App\Models\StudyMaterial;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\FeeGroup;
use App\Models\FeeMaster;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\StudentTransport;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\StudentHostel;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Notice;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ROLES & PERMISSIONS
        $roles = [
            'super-admin' => 'Super Administrator / Principal',
            'admin' => 'School Administrator',
            'teacher' => 'Faculty Teacher',
            'student' => 'Enrolled Student',
            'parent' => 'Student Guardian / Parent',
            'accountant' => 'Finance & Fee Officer',
            'librarian' => 'Library In-charge',
        ];

        $roleModels = [];
        foreach ($roles as $name => $displayName) {
            $roleModels[$name] = Role::create([
                'name' => $name,
                'display_name' => $displayName,
                'guard_name' => 'web',
            ]);
        }

        $permissions = [
            'academic.manage' => 'Manage Classes & Subjects',
            'student.admit' => 'Admit New Students',
            'student.view' => 'View Student Profiles',
            'student.edit' => 'Edit Student Records',
            'teacher.manage' => 'Manage Teachers & Allocations',
            'attendance.mark' => 'Mark Daily Attendance',
            'attendance.view' => 'View Attendance Reports',
            'marks.entry' => 'Enter Exam Marks',
            'marks.publish' => 'Publish Report Cards',
            'lms.upload' => 'Upload Study Materials',
            'lms.assignment.create' => 'Create Homework & Assignments',
            'lms.assignment.grade' => 'Grade Student Submissions',
            'fees.manage' => 'Manage Fee Masters & Invoices',
            'fees.collect' => 'Record Fee Payments',
            'payroll.generate' => 'Generate Teacher Payroll',
            'auxiliary.manage' => 'Manage Library, Transport, Hostel & Inventory',
            'notices.broadcast' => 'Publish School Notices',
        ];

        foreach ($permissions as $pName => $pDisplay) {
            $perm = Permission::create([
                'name' => $pName,
                'display_name' => $pDisplay,
                'group' => explode('.', $pName)[0],
                'guard_name' => 'web',
            ]);
            // Give all permissions to super-admin and admin
            $roleModels['super-admin']->givePermissionTo($perm);
            $roleModels['admin']->givePermissionTo($perm);
        }

        // Give teacher permissions
        $teacherPerms = ['attendance.mark', 'attendance.view', 'marks.entry', 'lms.upload', 'lms.assignment.create', 'lms.assignment.grade', 'student.view'];
        foreach ($teacherPerms as $tp) {
            $roleModels['teacher']->givePermissionTo($tp);
        }

        // 2. CREATE USERS & PROFILES
        $adminUser = User::create([
            'name' => 'Dr. Alexander Wright',
            'email' => 'admin@school.com',
            'phone' => '+1 (555) 019-2831',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('super-admin');

        // Teacher 1
        $teacherUser1 = User::create([
            'name' => 'Prof. Marcus Vance',
            'email' => 'teacher@school.com',
            'phone' => '+1 (555) 019-4820',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $teacherUser1->assignRole('teacher');
        $teacherProf1 = TeacherProfile::create([
            'user_id' => $teacherUser1->id,
            'employee_code' => 'EMP-2026-001',
            'qualification' => 'M.Sc. Mathematics, B.Ed',
            'designation' => 'Senior Math Faculty',
            'joining_date' => '2022-08-15',
            'basic_salary' => 5500.00,
            'address' => '742 Evergreen Terrace, Springfield',
        ]);

        // Teacher 2
        $teacherUser2 = User::create([
            'name' => 'Dr. Sarah Jenkins',
            'email' => 'sarah@school.com',
            'phone' => '+1 (555) 019-4821',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $teacherUser2->assignRole('teacher');
        $teacherProf2 = TeacherProfile::create([
            'user_id' => $teacherUser2->id,
            'employee_code' => 'EMP-2026-002',
            'qualification' => 'Ph.D. Computer Science',
            'designation' => 'HOD Computer Science & Robotics',
            'joining_date' => '2023-01-10',
            'basic_salary' => 6200.00,
            'address' => '104 Silicon Way, Tech Park',
        ]);

        // Parent User
        $parentUser = User::create([
            'name' => 'Robert & Clara Davis',
            'email' => 'parent@school.com',
            'phone' => '+1 (555) 019-9944',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $parentUser->assignRole('parent');
        $parentProf = ParentProfile::create([
            'user_id' => $parentUser->id,
            'occupation' => 'Architect / Civil Engineer',
            'identity_card_no' => 'IDN-9847291-B',
            'address' => '454 Oakridge Blvd, Metro City',
            'emergency_contact' => '+1 (555) 998-1122',
        ]);

        // 3. ACADEMIC SETUP
        $acadYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $classes = [
            ['name' => 'Grade 9', 'numeric_code' => 9],
            ['name' => 'Grade 10', 'numeric_code' => 10],
            ['name' => 'Grade 11', 'numeric_code' => 11],
            ['name' => 'Grade 12', 'numeric_code' => 12],
        ];

        $classModels = [];
        foreach ($classes as $c) {
            $classModels[$c['numeric_code']] = SchoolClass::create($c);
        }

        // Sections
        $sec10A = Section::create([
            'class_id' => $classModels[10]->id,
            'name' => 'Section A (Honors)',
            'capacity' => 35,
            'class_teacher_id' => $teacherProf1->id,
        ]);
        $sec10B = Section::create([
            'class_id' => $classModels[10]->id,
            'name' => 'Section B',
            'capacity' => 40,
            'class_teacher_id' => $teacherProf2->id,
        ]);
        $sec9A = Section::create([
            'class_id' => $classModels[9]->id,
            'name' => 'Section A',
            'capacity' => 35,
            'class_teacher_id' => $teacherProf1->id,
        ]);

        // Subjects for Grade 10
        $subMath = Subject::create([
            'class_id' => $classModels[10]->id,
            'name' => 'Advanced Mathematics',
            'code' => 'MTH-101',
            'pass_marks' => 40,
            'max_marks' => 100,
            'type' => 'theory',
        ]);
        $subCS = Subject::create([
            'class_id' => $classModels[10]->id,
            'name' => 'Computer Science & AI',
            'code' => 'CS-102',
            'pass_marks' => 40,
            'max_marks' => 100,
            'type' => 'both',
        ]);
        $subPhys = Subject::create([
            'class_id' => $classModels[10]->id,
            'name' => 'Physics & Quantum Mechanics',
            'code' => 'PHY-103',
            'pass_marks' => 40,
            'max_marks' => 100,
            'type' => 'both',
        ]);
        $subEng = Subject::create([
            'class_id' => $classModels[10]->id,
            'name' => 'English Literature & Rhetoric',
            'code' => 'ENG-104',
            'pass_marks' => 40,
            'max_marks' => 100,
            'type' => 'theory',
        ]);

        // Teacher Subject Allocation
        TeacherSubject::create(['teacher_id' => $teacherProf1->id, 'subject_id' => $subMath->id, 'section_id' => $sec10A->id]);
        TeacherSubject::create(['teacher_id' => $teacherProf1->id, 'subject_id' => $subPhys->id, 'section_id' => $sec10A->id]);
        TeacherSubject::create(['teacher_id' => $teacherProf2->id, 'subject_id' => $subCS->id, 'section_id' => $sec10A->id]);
        TeacherSubject::create(['teacher_id' => $teacherProf2->id, 'subject_id' => $subCS->id, 'section_id' => $sec10B->id]);

        // Student 1 (Linked to parent)
        $studentUser1 = User::create([
            'name' => 'Ethan Davis',
            'email' => 'student@school.com',
            'phone' => '+1 (555) 019-8833',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $studentUser1->assignRole('student');
        $studentProf1 = StudentProfile::create([
            'user_id' => $studentUser1->id,
            'parent_id' => $parentProf->id,
            'class_id' => $classModels[10]->id,
            'section_id' => $sec10A->id,
            'roll_number' => '10-01',
            'admission_number' => 'ADM-2026-1001',
            'admission_date' => '2026-08-01',
            'date_of_birth' => '2010-04-14',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => '454 Oakridge Blvd, Metro City',
        ]);

        // Student 2 (Sister - second child of parent)
        $studentUser2 = User::create([
            'name' => 'Emma Davis',
            'email' => 'emma@school.com',
            'phone' => '+1 (555) 019-8834',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $studentUser2->assignRole('student');
        $studentProf2 = StudentProfile::create([
            'user_id' => $studentUser2->id,
            'parent_id' => $parentProf->id,
            'class_id' => $classModels[9]->id,
            'section_id' => $sec9A->id,
            'roll_number' => '09-04',
            'admission_number' => 'ADM-2026-0904',
            'admission_date' => '2026-08-01',
            'date_of_birth' => '2011-09-22',
            'gender' => 'female',
            'blood_group' => 'A+',
            'address' => '454 Oakridge Blvd, Metro City',
        ]);

        // Student 3 (Other student)
        $studentUser3 = User::create([
            'name' => 'Lucas Sterling',
            'email' => 'lucas@school.com',
            'phone' => '+1 (555) 019-8835',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $studentUser3->assignRole('student');
        $studentProf3 = StudentProfile::create([
            'user_id' => $studentUser3->id,
            'parent_id' => null,
            'class_id' => $classModels[10]->id,
            'section_id' => $sec10A->id,
            'roll_number' => '10-02',
            'admission_number' => 'ADM-2026-1002',
            'admission_date' => '2026-08-02',
            'date_of_birth' => '2010-07-19',
            'gender' => 'male',
            'blood_group' => 'B+',
            'address' => '12 High Street, District 4',
        ]);

        // 4. TIME SLOTS & TIMETABLE
        $slot1 = TimeSlot::create(['name' => 'Period 1', 'start_time' => '08:30:00', 'end_time' => '09:20:00', 'is_break' => false]);
        $slot2 = TimeSlot::create(['name' => 'Period 2', 'start_time' => '09:25:00', 'end_time' => '10:15:00', 'is_break' => false]);
        $slotBreak = TimeSlot::create(['name' => 'Morning Recess', 'start_time' => '10:15:00', 'end_time' => '10:35:00', 'is_break' => true]);
        $slot3 = TimeSlot::create(['name' => 'Period 3', 'start_time' => '10:35:00', 'end_time' => '11:25:00', 'is_break' => false]);
        $slot4 = TimeSlot::create(['name' => 'Period 4', 'start_time' => '11:30:00', 'end_time' => '12:20:00', 'is_break' => false]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($days as $day) {
            Timetable::create(['section_id' => $sec10A->id, 'subject_id' => $subMath->id, 'teacher_id' => $teacherProf1->id, 'time_slot_id' => $slot1->id, 'day_of_week' => $day, 'room_number' => 'Lab 301']);
            Timetable::create(['section_id' => $sec10A->id, 'subject_id' => $subCS->id, 'teacher_id' => $teacherProf2->id, 'time_slot_id' => $slot2->id, 'day_of_week' => $day, 'room_number' => 'Turing Lab']);
            Timetable::create(['section_id' => $sec10A->id, 'subject_id' => $subPhys->id, 'teacher_id' => $teacherProf1->id, 'time_slot_id' => $slot3->id, 'day_of_week' => $day, 'room_number' => 'Physics Wing A']);
        }

        // 5. ATTENDANCE SEEDING (Past 5 days)
        for ($i = 4; $i >= 0; $i--) {
            $attDate = now()->subDays($i)->format('Y-m-d');
            Attendance::create([
                'student_id' => $studentProf1->id,
                'section_id' => $sec10A->id,
                'academic_year_id' => $acadYear->id,
                'date' => $attDate,
                'status' => $i === 1 ? 'late' : 'present',
                'remarks' => $i === 1 ? 'Traffic delay on 5th Ave' : 'On time',
                'marked_by' => $teacherUser1->id,
            ]);
            Attendance::create([
                'student_id' => $studentProf3->id,
                'section_id' => $sec10A->id,
                'academic_year_id' => $acadYear->id,
                'date' => $attDate,
                'status' => $i === 3 ? 'absent' : 'present',
                'remarks' => $i === 3 ? 'Medical leave' : 'On time',
                'marked_by' => $teacherUser1->id,
            ]);
        }

        // 6. GRADE RULES
        $gradeRules = [
            ['grade_name' => 'A+', 'min_percentage' => 90.00, 'max_percentage' => 100.00, 'grade_point' => 4.00, 'remarks' => 'Outstanding'],
            ['grade_name' => 'A',  'min_percentage' => 80.00, 'max_percentage' => 89.99,  'grade_point' => 3.70, 'remarks' => 'Excellent'],
            ['grade_name' => 'B+', 'min_percentage' => 70.00, 'max_percentage' => 79.99,  'grade_point' => 3.30, 'remarks' => 'Very Good'],
            ['grade_name' => 'B',  'min_percentage' => 60.00, 'max_percentage' => 69.99,  'grade_point' => 3.00, 'remarks' => 'Good'],
            ['grade_name' => 'C',  'min_percentage' => 50.00, 'max_percentage' => 59.99,  'grade_point' => 2.00, 'remarks' => 'Satisfactory'],
            ['grade_name' => 'D',  'min_percentage' => 40.00, 'max_percentage' => 49.99,  'grade_point' => 1.00, 'remarks' => 'Pass'],
            ['grade_name' => 'F',  'min_percentage' => 0.00,  'max_percentage' => 39.99,  'grade_point' => 0.00, 'remarks' => 'Needs Improvement / Fail'],
        ];
        foreach ($gradeRules as $gr) {
            GradeRule::create($gr);
        }

        // 7. EXAM & MARKS
        $exam = Exam::create([
            'academic_year_id' => $acadYear->id,
            'name' => 'Mid-Term Examination 2026',
            'start_date' => '2026-10-15',
            'end_date' => '2026-10-25',
            'is_published' => true,
        ]);

        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subMath->id, 'section_id' => $sec10A->id, 'exam_date' => '2026-10-15', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'room_number' => 'Hall A']);
        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subCS->id, 'section_id' => $sec10A->id, 'exam_date' => '2026-10-17', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'room_number' => 'Lab 1']);
        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subPhys->id, 'section_id' => $sec10A->id, 'exam_date' => '2026-10-19', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'room_number' => 'Hall B']);
        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subEng->id, 'section_id' => $sec10A->id, 'exam_date' => '2026-10-21', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'room_number' => 'Hall A']);

        // Marks for Ethan Davis
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subMath->id, 'student_id' => $studentProf1->id, 'marks_obtained' => 94.50, 'remarks' => 'Exceptional algebra solutions', 'entered_by' => $teacherUser1->id]);
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subCS->id, 'student_id' => $studentProf1->id, 'marks_obtained' => 98.00, 'remarks' => 'Flawless recursion algorithm', 'entered_by' => $teacherUser2->id]);
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subPhys->id, 'student_id' => $studentProf1->id, 'marks_obtained' => 88.00, 'remarks' => 'Very solid laboratory report', 'entered_by' => $teacherUser1->id]);
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subEng->id, 'student_id' => $studentProf1->id, 'marks_obtained' => 85.00, 'remarks' => 'Engaging essay structure', 'entered_by' => $teacherUser1->id]);

        // Marks for Lucas Sterling
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subMath->id, 'student_id' => $studentProf3->id, 'marks_obtained' => 76.00, 'remarks' => 'Good effort, revise trigonometry', 'entered_by' => $teacherUser1->id]);
        Mark::create(['exam_id' => $exam->id, 'subject_id' => $subCS->id, 'student_id' => $studentProf3->id, 'marks_obtained' => 91.00, 'remarks' => 'Great OOP design patterns', 'entered_by' => $teacherUser2->id]);

        // 8. LMS STUDY MATERIALS & ASSIGNMENTS
        StudyMaterial::create([
            'subject_id' => $subMath->id,
            'section_id' => $sec10A->id,
            'teacher_id' => $teacherProf1->id,
            'title' => 'Calculus & Limits Master Handout',
            'description' => 'Comprehensive formula sheet and 50 solved differential calculus problems for term prep.',
            'file_path' => 'materials/calculus_handout_2026.pdf',
            'file_type' => 'pdf',
        ]);
        StudyMaterial::create([
            'subject_id' => $subCS->id,
            'section_id' => $sec10A->id,
            'teacher_id' => $teacherProf2->id,
            'title' => 'Data Structures in Python & PHP Cheat Sheet',
            'description' => 'Trees, Graphs, HashTables, and Big-O computational complexity notes.',
            'file_path' => 'materials/data_structures_core.pdf',
            'file_type' => 'pdf',
        ]);

        $assignment = Assignment::create([
            'subject_id' => $subCS->id,
            'section_id' => $sec10A->id,
            'teacher_id' => $teacherProf2->id,
            'title' => 'Project: Neural Network Classifier from Scratch',
            'description' => 'Implement a simple 2-layer perceptron neural network in Python without using external ML libraries.',
            'attachment_path' => 'assignments/neural_net_specs.pdf',
            'due_date' => now()->addDays(5),
            'max_marks' => 100,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $studentProf1->id,
            'file_path' => 'submissions/ethan_davis_nn_project.zip',
            'submitted_at' => now()->subDay(),
            'marks_obtained' => 96.00,
            'feedback' => 'Brilliant vectorization! Forward & backprop matrices are well documented.',
            'status' => 'graded',
        ]);

        // 9. FINANCE, FEES & INVOICES
        $feeTuition = FeeGroup::create(['name' => 'Monthly Tuition Fee', 'description' => 'Standard monthly curriculum & instructional fees']);
        $feeLab = FeeGroup::create(['name' => 'Science & Robotics Lab Fee', 'description' => 'Covers equipment, computing servers & chemicals']);
        $feeAdmission = FeeGroup::create(['name' => 'Annual Admission & Registration', 'description' => 'One-time registration fee for academic term']);

        FeeMaster::create(['class_id' => $classModels[10]->id, 'fee_group_id' => $feeTuition->id, 'amount' => 450.00, 'due_date' => '2026-09-10']);
        FeeMaster::create(['class_id' => $classModels[10]->id, 'fee_group_id' => $feeLab->id, 'amount' => 120.00, 'due_date' => '2026-09-10']);

        // Invoices for Ethan Davis
        $inv1 = FeeInvoice::create([
            'student_id' => $studentProf1->id,
            'invoice_number' => 'INV-2026-00891',
            'title' => 'Term 1 Tuition & Robotics Lab Fee',
            'total_amount' => 570.00,
            'paid_amount' => 570.00,
            'due_date' => '2026-08-30',
            'status' => 'paid',
        ]);
        FeePayment::create([
            'fee_invoice_id' => $inv1->id,
            'transaction_id' => 'TXN-9982741-CARD',
            'amount_paid' => 570.00,
            'payment_method' => 'card',
            'paid_at' => now()->subDays(10),
            'notes' => 'Paid via Parent Visa Portal',
            'received_by' => $adminUser->id,
        ]);

        $inv2 = FeeInvoice::create([
            'student_id' => $studentProf1->id,
            'invoice_number' => 'INV-2026-01042',
            'title' => 'Term 2 Tuition & Science Lab Fee',
            'total_amount' => 570.00,
            'paid_amount' => 0.00,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'unpaid',
        ]);

        // Payroll for Teachers
        Payroll::create([
            'teacher_id' => $teacherProf1->id,
            'month_year' => 'July 2026',
            'basic_salary' => 5500.00,
            'allowances' => 400.00,
            'deductions' => 150.00,
            'net_salary' => 5750.00,
            'status' => 'paid',
            'paid_at' => '2026-07-31',
            'payment_method' => 'Direct Bank Deposit',
        ]);
        Payroll::create([
            'teacher_id' => $teacherProf2->id,
            'month_year' => 'July 2026',
            'basic_salary' => 6200.00,
            'allowances' => 500.00,
            'deductions' => 200.00,
            'net_salary' => 6500.00,
            'status' => 'paid',
            'paid_at' => '2026-07-31',
            'payment_method' => 'Direct Bank Deposit',
        ]);

        // 10. AUXILIARIES (Library, Transport, Hostel, Inventory, Notices)
        $book1 = Book::create(['title' => 'Clean Code: A Handbook of Agile Craftsmanship', 'isbn' => '978-0132350884', 'author' => 'Robert C. Martin', 'publisher' => 'Prentice Hall', 'quantity' => 5, 'rack_number' => 'CS-RACK-02']);
        $book2 = Book::create(['title' => 'Calculus: Early Transcendentals (8th Ed)', 'isbn' => '978-1285741550', 'author' => 'James Stewart', 'publisher' => 'Cengage Learning', 'quantity' => 12, 'rack_number' => 'MTH-RACK-01']);
        BookIssue::create([
            'book_id' => $book1->id,
            'user_id' => $studentUser1->id,
            'issue_date' => now()->subDays(7)->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'issued',
        ]);

        $route1 = TransportRoute::create(['name' => 'Route North #1 (Oakridge - Downtown - Campus)', 'fare' => 85.00, 'description' => 'Morning pickup 07:15 AM from Oakridge Plaza']);
        $veh1 = Vehicle::create(['vehicle_number' => 'BUS-2026-08', 'driver_name' => 'Captain Jonathan Cole', 'driver_phone' => '+1 (555) 302-9911', 'capacity' => 38]);
        StudentTransport::create(['student_id' => $studentProf1->id, 'route_id' => $route1->id, 'vehicle_id' => $veh1->id]);

        $hostel = Hostel::create(['name' => 'Newton Science Scholars Hostel', 'type' => 'boys', 'address' => 'East Campus Residence Block B']);
        $room = HostelRoom::create(['hostel_id' => $hostel->id, 'room_number' => 'B-304', 'capacity' => 2, 'cost_per_bed' => 350.00]);
        StudentHostel::create(['student_id' => $studentProf1->id, 'room_id' => $room->id]);

        $catLab = InventoryCategory::create(['name' => 'Robotics & Microcontrollers']);
        InventoryItem::create(['category_id' => $catLab->id, 'item_name' => 'Arduino Mega 2560 Starter Kit', 'quantity' => 45, 'unit_price' => 38.50]);
        InventoryItem::create(['category_id' => $catLab->id, 'item_name' => 'Raspberry Pi 5 (8GB RAM)', 'quantity' => 20, 'unit_price' => 85.00]);

        // Broadcast Notices
        Notice::create([
            'title' => 'Annual Science & AI Innovation Expo 2026',
            'content' => 'We are thrilled to announce our Annual School Science Fair and AI Robotics Exhibition scheduled for November 12th. All students Grade 8 through 12 are encouraged to submit their project proposals to their respective department faculty.',
            'target_role' => 'all',
            'created_by' => $adminUser->id,
        ]);
        Notice::create([
            'title' => 'Teacher Professional Development Workshop: Adaptive E-Learning',
            'content' => 'Mandatory pedagogical workshop for all faculty members this Friday at 03:00 PM in the Digital Auditorium. Refreshments will be served.',
            'target_role' => 'teacher',
            'created_by' => $adminUser->id,
        ]);
        Notice::create([
            'title' => 'Term 2 Fee Invoice Notification & Online Payment Portal',
            'content' => 'Term 2 invoices have been posted to the Parent and Student Portals. Payments can be settled online with credit/debit card with zero convenience surcharge.',
            'target_role' => 'parent',
            'created_by' => $adminUser->id,
        ]);
    }
}
