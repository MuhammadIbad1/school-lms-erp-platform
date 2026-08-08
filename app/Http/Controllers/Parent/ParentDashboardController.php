<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentProfile;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use App\Models\Notice;
use App\Models\Mark;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $parent = Auth::user()->parentProfile;

        if (!$parent) {
            abort(403, 'Parent guardian profile not found.');
        }

        $children = $parent->students()->with(['user', 'schoolClass', 'section'])->get();

        if ($children->isEmpty()) {
            return view('parent.no_children');
        }

        $selectedChildId = $request->child_id ?? session('selected_child_id') ?? $children->first()->id;
        $selectedChild = $children->firstWhere('id', $selectedChildId) ?? $children->first();

        // Store selected child in session for quick navigation
        session(['selected_child_id' => $selectedChild->id]);

        // Child's Attendance
        $attendances = Attendance::where('student_id', $selectedChild->id)->latest('date')->take(10)->get();
        $attendancePercentage = $selectedChild->attendance_percentage;

        // Child's Fee Invoices
        $invoices = FeeInvoice::where('student_id', $selectedChild->id)->latest()->get();
        $unpaidBalance = $invoices->where('status', '!=', 'paid')->sum('total_amount') - $invoices->where('status', '!=', 'paid')->sum('paid_amount');

        // Recent Marks
        $recentMarks = Mark::with(['subject', 'exam'])
            ->where('student_id', $selectedChild->id)
            ->latest()
            ->take(5)
            ->get();

        // Notices for Parents
        $notices = Notice::whereIn('target_role', ['all', 'parent'])->latest()->take(4)->get();

        return view('parent.dashboard', compact(
            'parent',
            'children',
            'selectedChild',
            'attendances',
            'attendancePercentage',
            'invoices',
            'unpaidBalance',
            'recentMarks',
            'notices'
        ));
    }
}
