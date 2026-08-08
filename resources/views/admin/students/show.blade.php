@extends('layouts.app')

@section('title', 'Student Profile: ' . $student->user->name)
@section('header', 'Student 360° Profile')
@section('subheader', 'Academic, Attendance, Exam Grades & Ledger Overview')

@section('content')
<div class="space-y-6">
    
    <!-- Top Identity Glass Banner -->
    <div class="glass-card p-6 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-xl shadow-indigo-600/30">
                {{ strtoupper(substr($student->user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white font-display">{{ $student->user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $student->user->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                        {{ $student->user->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Admission #: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $student->admission_number }}</span> | 
                    Class: <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $student->schoolClass->name }} - {{ $student->section->name }}</span> | 
                    Roll: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->roll_number }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-center">
                <p class="text-[10px] font-bold uppercase text-slate-400">Attendance Rate</p>
                <p class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $student->attendance_percentage }}%</p>
            </div>
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-center">
                <p class="text-[10px] font-bold uppercase text-slate-400">Invoices</p>
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $student->feeInvoices->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Personal & Auxiliaries (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Personal Info -->
            <div class="glass-card p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Personal Demographics</h3>
                <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Email Address</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->user->email }}</span>
                </div>
                <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Phone</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->user->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Gender / Blood</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $student->gender }} ({{ $student->blood_group ?? 'Unknown' }})</span>
                </div>
                <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Date of Birth</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->date_of_birth?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-xs py-1.5">
                    <span class="text-slate-400 font-medium">Admission Date</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->admission_date->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Parent Guardian Info -->
            <div class="glass-card p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Guardian Profile</h3>
                @if($student->parent)
                    <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">Guardian Name</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->parent->user->name }}</span>
                    </div>
                    <div class="flex justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">Guardian Email</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->parent->user->email }}</span>
                    </div>
                    <div class="flex justify-between text-xs py-1.5">
                        <span class="text-slate-400 font-medium">Occupation</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->parent->occupation ?? 'Guardian' }}</span>
                    </div>
                @else
                    <p class="text-xs text-slate-400">No linked guardian profile on record.</p>
                @endif
            </div>

            <!-- Auxiliaries -->
            <div class="glass-card p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Auxiliary Allocations</h3>
                <div class="text-xs">
                    <p class="text-slate-400 font-medium">Transport Fleet:</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                        {{ $student->transport ? $student->transport->route->name . ' (' . $student->transport->vehicle->vehicle_number . ')' : 'Self Commute / Day Scholar' }}
                    </p>
                </div>
                <div class="text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-slate-400 font-medium">Hostel Dormitory:</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                        {{ $student->hostel ? $student->hostel->room->hostel->name . ' - Room ' . $student->hostel->room->room_number : 'Day Scholar (No Hostel)' }}
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Column: Exam Marks & Attendance Ledger (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Exam Performance Grid -->
            <div class="flat-table-wrapper">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Exam Assessment Scores</h3>
                        <p class="text-xs text-slate-500">Subject marks evaluated against Grade Rules</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3.5">Exam Term</th>
                                <th class="px-5 py-3.5">Subject</th>
                                <th class="px-5 py-3.5">Marks Obtained</th>
                                <th class="px-5 py-3.5">Percentage</th>
                                <th class="px-5 py-3.5">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($student->marks as $mark)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $mark->exam->name }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-indigo-600 dark:text-indigo-400">{{ $mark->subject->name }}</td>
                                    <td class="px-5 py-3.5 font-mono font-bold">{{ $mark->marks_obtained }} / {{ $mark->subject->max_marks }}</td>
                                    <td class="px-5 py-3.5 font-bold">{{ $mark->percentage }}%</td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                                            {{ $mark->grade?->grade_name ?? 'Pass' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">No exam marks entered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fee Invoices Ledger -->
            <div class="flat-table-wrapper">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Fee Invoices & Payments</h3>
                        <p class="text-xs text-slate-500">Student billing ledger and transaction history</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3.5">Invoice #</th>
                                <th class="px-5 py-3.5">Title</th>
                                <th class="px-5 py-3.5">Total Amount</th>
                                <th class="px-5 py-3.5">Paid Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($student->feeInvoices as $inv)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3.5 font-mono font-bold text-slate-900 dark:text-white">{{ $inv->invoice_number }}</td>
                                    <td class="px-5 py-3.5 font-medium">{{ $inv->title }}</td>
                                    <td class="px-5 py-3.5 font-bold">${{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="px-5 py-3.5 font-bold text-emerald-600">${{ number_format($inv->paid_amount, 2) }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $inv->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">No invoices generated for this student.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
