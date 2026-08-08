<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeGroup;
use App\Models\FeeMaster;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    public function index()
    {
        $feeGroups = FeeGroup::with('feeMasters.schoolClass')->get();
        $classes = SchoolClass::all();

        $invoices = FeeInvoice::with(['student.user', 'student.schoolClass', 'student.section', 'payments'])
            ->latest()
            ->paginate(15);

        $pendingApprovalInvoices = FeeInvoice::with(['student.user', 'student.schoolClass', 'student.section'])
            ->where('status', 'pending_approval')
            ->latest()
            ->get();

        $totalInvoiced = FeeInvoice::sum('total_amount');
        $totalCollected = FeeInvoice::sum('paid_amount');
        $totalPending = $totalInvoiced - $totalCollected;

        return view('admin.fees.index', compact(
            'feeGroups',
            'classes',
            'invoices',
            'pendingApprovalInvoices',
            'totalInvoiced',
            'totalCollected',
            'totalPending'
        ));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        FeeGroup::create($validated);
        return back()->with('success', 'Fee category created successfully!');
    }

    public function storeMaster(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'fee_group_id' => ['required', 'exists:fee_groups,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'due_date' => ['required', 'date'],
        ]);

        FeeMaster::create($validated);
        return back()->with('success', 'Fee rule assigned to class successfully!');
    }

    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
        ]);

        $students = StudentProfile::where('class_id', $validated['class_id'])->get();
        $feeMasters = FeeMaster::where('class_id', $validated['class_id'])->get();
        $totalAmount = $feeMasters->sum('amount');

        if ($totalAmount <= 0) {
            return back()->with('error', 'No fee rules configured for this class. Please setup fee master first.');
        }

        if ($students->isEmpty()) {
            return back()->with('error', 'No students enrolled in this class.');
        }

        $count = 0;
        DB::transaction(function () use ($students, $validated, $totalAmount, &$count) {
            foreach ($students as $st) {
                FeeInvoice::create([
                    'student_id' => $st->id,
                    'invoice_number' => 'INV-' . date('Ym') . '-' . rand(1000, 9999) . '-' . $st->id,
                    'title' => $validated['title'],
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0.00,
                    'due_date' => $validated['due_date'],
                    'status' => 'unpaid',
                ]);
                $count++;
            }
        });

        return back()->with('success', "Batch generator completed: {$count} student invoices created successfully!");
    }

    public function recordPayment(Request $request)
    {
        $validated = $request->validate([
            'fee_invoice_id' => ['required', 'exists:fee_invoices,id'],
            'amount_paid' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,online'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $invoice = FeeInvoice::findOrFail($validated['fee_invoice_id']);

        DB::transaction(function () use ($validated, $invoice) {
            $newPaid = $invoice->paid_amount + $validated['amount_paid'];
            $status = $newPaid >= $invoice->total_amount ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $status,
            ]);

            FeePayment::create([
                'fee_invoice_id' => $invoice->id,
                'transaction_id' => 'TXN-' . date('YmdHis') . '-' . rand(100, 999),
                'amount_paid' => $validated['amount_paid'],
                'payment_method' => $validated['payment_method'],
                'paid_at' => now(),
                'notes' => $validated['notes'] ?? 'Manual Payment Entry',
                'received_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Fee payment recorded and invoice updated!');
    }

    public function approveCashPayment(Request $request, FeeInvoice $invoice)
    {
        $due = $invoice->due_balance;

        DB::transaction(function () use ($invoice, $due) {
            $invoice->update([
                'paid_amount' => $invoice->total_amount,
                'status' => 'paid',
            ]);

            FeePayment::create([
                'fee_invoice_id' => $invoice->id,
                'transaction_id' => 'CASH-HR-' . date('YmdHis') . '-' . rand(100, 999),
                'amount_paid' => $due,
                'payment_method' => 'cash',
                'paid_at' => now(),
                'notes' => 'Cash received & approved at School Accounts Desk by ' . Auth::user()->name,
                'received_by' => Auth::id(),
            ]);
        });

        return back()->with('success', "Cash payment verified & approved for student {$invoice->student->user->name}! Invoice is now PAID.");
    }

    public function rejectPaymentRequest(FeeInvoice $invoice)
    {
        $invoice->update([
            'status' => 'unpaid',
        ]);

        return back()->with('info', "Payment request for invoice {$invoice->invoice_number} was rejected and reverted to unpaid.");
    }

    public function showReceipt(FeeInvoice $invoice)
    {
        $invoice->load(['student.user', 'student.schoolClass', 'student.section', 'payments.receiver']);
        return view('admin.fees.receipt', compact('invoice'));
    }
}
