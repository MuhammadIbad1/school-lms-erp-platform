<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FeeInvoice;

class StudentFeeController extends Controller
{
    public function index()
    {
        $student = Auth::user()->studentProfile;

        $invoices = FeeInvoice::with(['payments.receiver'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $dueBalance = $totalInvoiced - $totalPaid;

        return view('student.fees.index', compact('invoices', 'totalInvoiced', 'totalPaid', 'dueBalance'));
    }
}
