<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Payroll;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\DB;

class AuxiliaryController extends Controller
{
    // ================= 1. LIBRARY =================
    public function library()
    {
        $books = Book::withCount('issues')->latest()->get();
        $issues = BookIssue::with(['book', 'user'])->latest()->paginate(15);
        $users = User::all();

        return view('admin.auxiliaries.library', compact('books', 'issues', 'users'));
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'unique:books,isbn'],
            'author' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'rack_number' => ['nullable', 'string', 'max:50'],
        ]);

        Book::create($validated);
        return back()->with('success', 'Book added to library catalog!');
    }

    public function issueBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'user_id' => ['required', 'exists:users,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after:issue_date'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        if ($book->quantity < 1) {
            return back()->with('error', 'Book is currently out of stock!');
        }

        DB::transaction(function () use ($validated, $book) {
            BookIssue::create([
                'book_id' => $book->id,
                'user_id' => $validated['user_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => 'issued',
            ]);
            $book->decrement('quantity');
        });

        return back()->with('success', 'Book issued successfully!');
    }

    public function returnBook(BookIssue $issue)
    {
        DB::transaction(function () use ($issue) {
            $issue->update([
                'return_date' => now()->format('Y-m-d'),
                'status' => 'returned',
            ]);
            $issue->book->increment('quantity');
        });

        return back()->with('success', 'Book returned and stock restored!');
    }

    // ================= 2. TRANSPORT =================
    public function transport()
    {
        $routes = TransportRoute::with('studentTransports.student.user')->get();
        $vehicles = Vehicle::all();
        $students = StudentProfile::with('user')->get();

        return view('admin.auxiliaries.transport', compact('routes', 'vehicles', 'students'));
    }

    public function storeRoute(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'fare' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        TransportRoute::create($validated);
        return back()->with('success', 'Transport route created!');
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => ['required', 'string', 'unique:vehicles,vehicle_number'],
            'driver_name' => ['required', 'string', 'max:100'],
            'driver_phone' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        Vehicle::create($validated);
        return back()->with('success', 'Vehicle registered to school fleet!');
    }

    // ================= 3. HOSTEL =================
    public function hostel()
    {
        $hostels = Hostel::with('rooms.students.student.user')->get();
        return view('admin.auxiliaries.hostel', compact('hostels'));
    }

    public function storeHostel(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:boys,girls,mixed'],
            'address' => ['nullable', 'string'],
        ]);

        Hostel::create($validated);
        return back()->with('success', 'Hostel building recorded!');
    }

    public function storeHostelRoom(Request $request)
    {
        $validated = $request->validate([
            'hostel_id' => ['required', 'exists:hostels,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'cost_per_bed' => ['required', 'numeric', 'min:0'],
        ]);

        HostelRoom::create($validated);
        return back()->with('success', 'Hostel room created!');
    }

    // ================= 4. INVENTORY =================
    public function inventory()
    {
        $categories = InventoryCategory::with('items')->get();
        $items = InventoryItem::with('category')->latest()->paginate(15);

        return view('admin.auxiliaries.inventory', compact('categories', 'items'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        InventoryCategory::create($validated);
        return back()->with('success', 'Inventory category created!');
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:inventory_categories,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        InventoryItem::create($validated);
        return back()->with('success', 'Inventory item logged!');
    }

    // ================= 5. PAYROLL =================
    public function payroll()
    {
        $teachers = TeacherProfile::with('user')->get();
        $payrolls = Payroll::with('teacher.user')->latest()->paginate(15);

        return view('admin.auxiliaries.payroll', compact('teachers', 'payrolls'));
    }

    public function generatePayroll(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:teacher_profiles,id'],
            'month_year' => ['required', 'string', 'max:50'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:100'],
        ]);

        $teacher = TeacherProfile::findOrFail($validated['teacher_id']);
        $allowances = $validated['allowances'] ?? 0.00;
        $deductions = $validated['deductions'] ?? 0.00;
        $netSalary = $teacher->basic_salary + $allowances - $deductions;

        Payroll::create([
            'teacher_id' => $teacher->id,
            'month_year' => $validated['month_year'],
            'basic_salary' => $teacher->basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => max(0, $netSalary),
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'] ?? 'Direct Bank Deposit',
        ]);

        return back()->with('success', "Salary voucher generated and marked paid for {$teacher->user->name}!");
    }

    public function showPayslip(Payroll $payroll)
    {
        $payroll->load(['teacher.user']);
        return view('admin.auxiliaries.payslip', compact('payroll'));
    }
}
