<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStudentSelfAccess
{
    /**
     * Prevent student from accessing or modifying other students' data.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('student')) {
            return $next($request);
        }

        $studentProfile = $user->studentProfile;
        if (!$studentProfile) {
            abort(403, 'Student profile not found.');
        }

        $studentId = $request->route('student') ?? $request->route('student_id') ?? $request->input('student_id');
        if ($studentId) {
            $id = is_object($studentId) ? $studentId->id : $studentId;
            if ((int)$id !== (int)$studentProfile->id) {
                abort(403, 'Forbidden. You may only access your personal academic records.');
            }
        }

        return $next($request);
    }
}
