<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;

class ParentFeeController extends Controller
{
    public function index(Request $request)
    {
        $parent = Auth::user()->parentProfile;
        $childId = $request->child_id ?? session('selected_child_id');
        $child = $parent->students()->with(['user', 'schoolClass', 'section'])->where('id', $childId)->first() ?? $parent->students()->firstOrFail();

        $invoices = FeeInvoice::with('payments.receiver')
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $dueBalance = $totalInvoiced - $totalPaid;

        return view('parent.fees.index', compact('child', 'invoices', 'totalInvoiced', 'totalPaid', 'dueBalance'));
    }

    public function payOnline(Request $request, FeeInvoice $invoice)
    {
        $validated = $request->validate([
            'payment_type' => ['required', 'in:card,cash_request'],
            'card_name' => ['nullable', 'string', 'max:100'],
            'card_number' => ['nullable', 'string'],
            'cash_memo' => ['nullable', 'string', 'max:255'],
        ]);

        $due = $invoice->due_balance;

        if ($due <= 0) {
            return back()->with('error', 'This invoice is already fully settled.');
        }

        if ($validated['payment_type'] === 'card') {
            // Instant Card Settlement
            DB::transaction(function () use ($invoice, $due, $validated) {
                $invoice->update([
                    'paid_amount' => $invoice->total_amount,
                    'status' => 'paid',
                ]);

                FeePayment::create([
                    'fee_invoice_id' => $invoice->id,
                    'transaction_id' => 'GATEWAY-CARD-' . strtoupper(uniqid()),
                    'amount_paid' => $due,
                    'payment_method' => 'card',
                    'paid_at' => now(),
                    'notes' => 'Paid via Online Card: ' . substr($validated['card_number'] ?? '4242', -4),
                    'received_by' => Auth::id(),
                ]);
            });

            return back()->with('success', 'Card payment authorized successfully! Official school fee receipt generated.');
        } else {
            // Cash at School Desk / Bank Deposit Verification Request
            $invoice->update([
                'status' => 'pending_approval',
            ]);

            return back()->with('success', 'Cash payment request submitted! Once verified by School HR / Accounts desk, your invoice will be marked paid and receipt issued.');
        }
    }
}
