<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response    
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        if ($role == 'admin' && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($role == 'dosen' && !$user->isDosen()) {
            abort(403, 'Unauthorized access.');
        }

        if ($role == 'mahasiswa' && !$user->isMahasiswa()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}