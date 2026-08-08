<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyParentChildAccess
{
    /**
     * Prevent IDOR by ensuring parent only accesses their own children's records.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('parent')) {
            return $next($request);
        }

        $parentProfile = $user->parentProfile;
        if (!$parentProfile) {
            abort(403, 'Parent profile not configured.');
        }

        // Check if student_id or child_id is in route parameters or query
        $studentId = $request->route('student') ?? $request->route('student_id') ?? $request->input('student_id');

        if ($studentId) {
            $id = is_object($studentId) ? $studentId->id : $studentId;
            $childExists = $parentProfile->students()->where('id', $id)->exists();

            if (!$childExists) {
                abort(403, 'Access denied. You can only view details for your linked children.');
            }
        }

        return $next($request);
    }
}
