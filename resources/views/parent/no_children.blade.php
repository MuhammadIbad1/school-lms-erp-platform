@extends('layouts.app')

@section('title', 'Parent Portal')
@section('header', 'Parent Portal')
@section('subheader', 'Child Guardian & Family Management')

@section('content')
<div class="glass-card p-12 text-center max-w-xl mx-auto space-y-4">
    <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-2xl font-bold">
        👨‍👩‍👧
    </div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Children Currently Linked</h3>
    <p class="text-xs text-slate-500 leading-relaxed">
        Your parent guardian profile is active, but no student profiles have been connected to your guardian account yet. Please contact the school admissions office with your guardian email (<span class="font-bold text-indigo-600">{{ auth()->user()->email }}</span>) to link your child.
    </p>
</div>
@endsection
