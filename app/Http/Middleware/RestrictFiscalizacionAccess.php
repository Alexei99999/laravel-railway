<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RestrictFiscalizacionAccess
{
    public function handle(Request $request, Closure $next, $type)
    {
        $user = auth()->user();
        if (!$user) {
            \Log::error('RestrictFiscalizacionAccess: Guest user', ['route' => $request->path()]);
            throw UnauthorizedException::notLoggedIn();
        }

        \Log::info('RestrictFiscalizacionAccess: Checking', [
            'user_id' => $user->id,
            'type' => $type,
            'route' => $request->path(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);

        if ($type === 'fiscalizacions' && $user->hasPermissionTo('ver-fiscalizacion') && !$user->hasPermissionTo('ver-fiscalizacion-all')) {
            return $next($request);
        }

        if ($type === 'fiscalizacions-all' && $user->hasPermissionTo('ver-fiscalizacion-all') && !$user->hasPermissionTo('ver-fiscalizacion')) {
            return $next($request);
        }

        \Log::error('RestrictFiscalizacionAccess: Unauthorized', [
            'user_id' => $user->id,
            'type' => $type,
            'route' => $request->path()
        ]);
        throw UnauthorizedException::forPermissions([$type === 'fiscalizacions' ? 'ver-fiscalizacion' : 'ver-fiscalizacion-all']);
    }
}
