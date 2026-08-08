@extends('layouts.app')

@section('title', 'Admit New Student')
@section('header', 'Student Admission Hub')
@section('subheader', 'Multi-Step Enrollment Wizard & Auto Fee Invoice Generator')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card p-8">
        
        <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-8" x-data="{ parentType: 'new' }">
            @csrf

            <!-- STEP 1: STUDENT CREDENTIALS & DEMOGRAPHICS -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Student Personal Details & Login Account</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Benjamin Vance" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Student Email (Login ID)</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="student@school.com" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Account Password</label>
                        <input type="password" name="password" required value="password123" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 019-2831" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', '2010-01-01') }}" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Gender</label>
                        <select name="gender" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-slate-200/60 dark:border-slate-800/60">

            <!-- STEP 2: ACADEMIC PLACEMENT -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Academic Placement & Admission Roster</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class</label>
                        <select name="class_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Section</label>
                        <select name="section_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            @foreach($classes as $c)
                                @foreach($c->sections as $sec)
                                    <option value="{{ $sec->id }}">{{ $c->name }} - {{ $sec->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Admission Number</label>
                        <input type="text" name="admission_number" value="ADM-2026-{{ rand(1000, 9999) }}" required 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class Roll Number</label>
                        <input type="text" name="roll_number" value="{{ rand(1, 40) }}" required 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Admission Date</label>
                        <input type="date" name="admission_date" value="{{ date('Y-m-d') }}" required 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Blood Group</label>
                        <input type="text" name="blood_group" placeholder="O+, A+, B+, etc." 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
            </div>

            <hr class="border-slate-200/60 dark:border-slate-800/60">

            <!-- STEP 3: PARENT / GUARDIAN LINK -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Parent / Guardian Connection</h3>
                </div>
                
                <div class="flex items-center space-x-6 mb-4">
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                        <input type="radio" name="parent_type" value="new" x-model="parentType" class="text-indigo-600">
                        <span>Register New Parent Profile</span>
                    </label>
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                        <input type="radio" name="parent_type" value="existing" x-model="parentType" class="text-indigo-600">
                        <span>Link to Existing Registered Parent</span>
                    </label>
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                        <input type="radio" name="parent_type" value="none" x-model="parentType" class="text-indigo-600">
                        <span>Self / Independent</span>
                    </label>
                </div>

                <!-- Existing Parent Selector -->
                <div x-show="parentType === 'existing'" class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Select Existing Parent Profile</label>
                    <select name="parent_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->user->name }} ({{ $p->user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- New Parent Fields -->
                <div x-show="parentType === 'new'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Guardian Name</label>
                        <input type="text" name="parent_name" placeholder="Robert Vance" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Guardian Email (Parent Portal Login)</label>
                        <input type="email" name="parent_email" placeholder="parent.vance@example.com" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Guardian Phone</label>
                        <input type="text" name="parent_phone" placeholder="+1 (555) 019-9944" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Occupation</label>
                        <input type="text" name="parent_occupation" placeholder="Software Engineer" 
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
            </div>

            <hr class="border-slate-200/60 dark:border-slate-800/60">

            <!-- STEP 4: AUXILIARIES & AUTOMATION -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Auxiliary Allocation & Automatic Invoicing</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Transport Route</label>
                        <select name="route_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            <option value="">-- No Bus Transport --</option>
                            @foreach($routes as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} (${{ $r->fare }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Bus Vehicle</label>
                        <select name="vehicle_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            <option value="">-- Select Bus Vehicle --</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->vehicle_number }} (Driver: {{ $v->driver_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Hostel Dormitory Room</label>
                        <select name="hostel_room_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                            <option value="">-- Day Scholar (No Hostel) --</option>
                            @foreach($hostelRooms as $hr)
                                <option value="{{ $hr->id }}">{{ $hr->hostel->name }} - Room {{ $hr->room_number }} (${{ $hr->cost_per_bed }}/mo)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="auto_generate_fee" value="1" checked id="autoFee" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="autoFee" class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Automatically Generate Admission & Term 1 Tuition Fee Invoice
                            <p class="text-[11px] font-normal text-slate-500">Instantly applies Fee Master rules for selected class upon completion.</p>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('admin.students.index') }}" class="px-6 py-3.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xl shadow-indigo-600/30 transition">
                    Complete Admission & Enroll Student
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
