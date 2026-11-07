<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $userRole = Role::tryFrom($user->role);

        $allowedRoles = array_filter(
            array_map(
                fn (string $role) => Role::tryFrom($role),
                $roles
            )
        );

        if (!$userRole || !in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }
}