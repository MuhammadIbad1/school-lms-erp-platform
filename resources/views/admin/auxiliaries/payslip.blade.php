<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payslip - {{ $payroll->teacher->employee_code }} - {{ $payroll->month_year }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .payslip-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-10 flex flex-col items-center justify-center">

    <!-- Top Action Bar (Hidden during Print / PDF Export) -->
    <div class="no-print mb-6 flex flex-wrap items-center gap-3">
        <button onclick="window.print()" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xl shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            🖨️ Print / Save as PDF
        </button>
        <button onclick="downloadAsHtml()" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xl shadow-emerald-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            💾 Download Payslip Document
        </button>
        <button onclick="window.close()" class="px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
            Close Window
        </button>
    </div>

    <!-- Official Payslip Sheet Card -->
    <div id="payslipContent" class="payslip-card w-full max-w-3xl bg-white rounded-3xl p-8 md:p-12 shadow-2xl border border-slate-200 text-slate-800">
        
        <!-- Header Institution Brand -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-6 mb-6">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md">
                        🎓
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900 font-display">EduNova Academy & College</h1>
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Faculty & Staff Compensation Slip</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Campus Wing 4 • Tax & Institutional ID: EDU-PAY-884920</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-100 text-emerald-800">
                    DISBURSED: {{ strtoupper($payroll->status) }}
                </span>
                <p class="text-xs font-mono font-bold text-slate-600 mt-2">Cycle: {{ $payroll->month_year }}</p>
            </div>
        </div>

        <!-- Faculty Information Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8 text-xs">
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Faculty Member</p>
                <p class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $payroll->teacher->user->name }}</p>
                <p class="text-[11px] text-slate-500">{{ $payroll->teacher->user->email }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Employee ID</p>
                <p class="font-mono font-bold text-indigo-600 text-sm mt-0.5">{{ $payroll->teacher->employee_code }}</p>
                <p class="text-[11px] text-slate-500">{{ $payroll->teacher->designation }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Qualification</p>
                <p class="font-semibold text-slate-800 mt-0.5">{{ $payroll->teacher->qualification }}</p>
                <p class="text-[11px] text-slate-500">Joined: {{ $payroll->teacher->joining_date->format('M Y') }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Payment Mode</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $payroll->payment_method ?? 'Direct Bank Wire' }}</p>
                <p class="text-[11px] text-slate-500">Date: {{ $payroll->paid_at ? $payroll->paid_at->format('M d, Y') : date('M d, Y') }}</p>
            </div>
        </div>

        <!-- Earnings & Deductions 2-Column Table -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-xs">
            
            <!-- Earnings Column -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="bg-slate-100 px-4 py-3 border-b border-slate-200 flex justify-between font-bold text-slate-800">
                    <span>EARNINGS & ALLOWANCES</span>
                    <span>AMOUNT ($)</span>
                </div>
                <div class="divide-y divide-slate-100 p-4 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Base Contractual Salary</span>
                        <span class="font-mono font-bold text-slate-900">${{ number_format($payroll->basic_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-600 font-medium">Academic & Research Allowances</span>
                        <span class="font-mono font-bold text-emerald-600">+${{ number_format($payroll->allowances, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t-2 border-slate-200 font-bold text-slate-900">
                        <span>Total Gross Earnings</span>
                        <span class="font-mono font-extrabold text-sm">${{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Deductions Column -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="bg-slate-100 px-4 py-3 border-b border-slate-200 flex justify-between font-bold text-slate-800">
                    <span>DEDUCTIONS & TAXES</span>
                    <span>AMOUNT ($)</span>
                </div>
                <div class="divide-y divide-slate-100 p-4 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Statutory Tax & Insurance Deductions</span>
                        <span class="font-mono font-bold text-rose-500">-${{ number_format($payroll->deductions, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-600 font-medium">Institutional Provident Fund</span>
                        <span class="font-mono font-bold text-slate-400">$0.00</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t-2 border-slate-200 font-bold text-slate-900">
                        <span>Total Deductions</span>
                        <span class="font-mono font-extrabold text-sm text-rose-600">-${{ number_format($payroll->deductions, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Net Disbursed Salary Banner -->
        <div class="p-6 rounded-2xl bg-indigo-50/80 border border-indigo-200 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-800">Net Take-Home Salary Disbursed</p>
                <p class="text-xs text-slate-500 mt-0.5">Credited to registered faculty bank account via electronic payroll wire</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-extrabold text-indigo-600 font-mono font-display">${{ number_format($payroll->net_salary, 2) }}</p>
            </div>
        </div>

        <!-- Institutional Sign-off -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-3 gap-6 text-center text-xs text-slate-400">
            <div>
                <div class="h-10 border-b border-slate-300 mb-2"></div>
                <p class="font-bold text-slate-800">{{ $payroll->teacher->user->name }}</p>
                <p class="text-[10px]">Faculty Employee Signature</p>
            </div>
            <div>
                <div class="h-10 border-b border-slate-300 mb-2"></div>
                <p class="font-bold text-slate-800">Accounts & Bursar</p>
                <p class="text-[10px]">Financial Comptroller</p>
            </div>
            <div>
                <div class="h-10 border-b border-slate-300 mb-2"></div>
                <p class="font-bold text-slate-800">Dr. Alexander Wright</p>
                <p class="text-[10px]">Dean & Principal Seal</p>
            </div>
        </div>

    </div>

    <script>
        function downloadAsHtml() {
            const content = document.getElementById('payslipContent').outerHTML;
            const fullHtml = `<!DOCTYPE html><html><head><title>Salary Slip - {{ $payroll->teacher->employee_code }}</title><script src="https://cdn.tailwindcss.com"><\/script><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"><style>body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;padding:2rem;display:flex;justify-content:center;}</style></head><body>${content}</body></html>`;
            
            const blob = new Blob([fullHtml], { type: 'text/html' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'Payslip_{{ $payroll->teacher->employee_code }}_{{ str_replace(" ", "_", $payroll->month_year) }}.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>

</body>
</html>
