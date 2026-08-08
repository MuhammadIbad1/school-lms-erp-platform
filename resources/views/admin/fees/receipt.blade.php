<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-10 flex flex-col items-center justify-center">

    <div class="no-print mb-6 flex items-center space-x-4">
        <button onclick="window.print()" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xl transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Official Receipt
        </button>
        <button onclick="window.close()" class="px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
            Close Window
        </button>
    </div>

    <!-- Official Receipt Card -->
    <div class="w-full max-w-2xl bg-white rounded-3xl p-8 md:p-12 shadow-2xl border border-slate-200">
        
        <!-- Header Brand -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-8 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 font-display">EduNova Academy & College</h1>
                <p class="text-xs text-slate-500 mt-1">Official Student Fee & Academic Invoice Voucher</p>
                <p class="text-xs text-slate-400">Tax ID: EDU-8947291 • Metro Campus Building 4</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    Status: {{ strtoupper($invoice->status) }}
                </span>
                <p class="text-xs font-mono font-bold text-slate-600 mt-2">{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <!-- Student & Date Details -->
        <div class="grid grid-cols-2 gap-6 mb-8 text-xs">
            <div>
                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Billed Student</p>
                <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ $invoice->student->user->name }}</h3>
                <p class="text-slate-600 mt-0.5">Admission #: {{ $invoice->student->admission_number }}</p>
                <p class="text-slate-600">Class: {{ $invoice->student->schoolClass->name }} - {{ $invoice->student->section->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Invoice Schedule</p>
                <p class="text-slate-600 mt-1 font-semibold">Due Date: <span class="font-bold text-slate-900">{{ $invoice->due_date->format('M d, Y') }}</span></p>
                <p class="text-slate-500 mt-0.5">Generated: {{ $invoice->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="w-full text-left text-xs mb-8">
            <thead class="border-y border-slate-200 text-slate-400 uppercase text-[10px] font-bold">
                <tr>
                    <th class="py-3">Description</th>
                    <th class="py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <tr>
                    <td class="py-4 font-semibold text-slate-900">
                        {{ $invoice->title }}
                        <span class="block text-[11px] font-normal text-slate-400">Institutional tuition and auxiliary services</span>
                    </td>
                    <td class="py-4 text-right font-mono font-bold text-slate-900">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
            <tfoot class="border-t-2 border-slate-900">
                <tr>
                    <td class="pt-4 font-bold text-slate-900">Total Billed</td>
                    <td class="pt-4 text-right font-mono font-extrabold text-slate-900 text-sm">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-bold text-emerald-600">Amount Paid</td>
                    <td class="py-1 text-right font-mono font-extrabold text-emerald-600">-${{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-bold text-rose-600">Balance Due</td>
                    <td class="py-1 text-right font-mono font-extrabold text-rose-600 text-base">${{ number_format($invoice->due_balance, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Payment History -->
        @if($invoice->payments->isNotEmpty())
            <div class="mb-8 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Verified Payment Audit Trail</p>
                @foreach($invoice->payments as $pmt)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-slate-200/60 last:border-0">
                        <div>
                            <span class="font-mono font-bold text-slate-700">{{ $pmt->transaction_id }}</span>
                            <span class="text-slate-400 ml-2">({{ strtoupper($pmt->payment_method) }})</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-600">+${{ number_format($pmt->amount_paid, 2) }}</span>
                            <span class="text-slate-400 text-[10px] ml-2">{{ $pmt->paid_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Footer Sign-off -->
        <div class="pt-8 border-t border-slate-200 flex items-center justify-between text-xs text-slate-400">
            <div>
                <p class="font-bold text-slate-800">EduNova Finance & Bursar Desk</p>
                <p>Computer-generated authorized receipt.</p>
            </div>
            <div class="w-32 border-b border-slate-400 pb-1 text-center font-serif text-slate-500 italic">
                Authorized Stamp
            </div>
        </div>

    </div>

</body>
</html>
