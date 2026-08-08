<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('author')->latest()->paginate(15);
        return view('admin.notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target_role' => ['required', 'in:all,teacher,student,parent,admin'],
        ]);

        $validated['created_by'] = Auth::id();

        Notice::create($validated);

        return back()->with('success', 'Official school announcement broadcasted successfully!');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice removed from bulletin.');
    }
}
