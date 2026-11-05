<?php

namespace Mapo89\LaravelAvatarManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-KEY');

        if (is_string($key) && $key !== '') {
            foreach (config('avatar-manager.api_keys', []) as $validKey) {
                if (is_string($validKey) && $validKey !== '' && hash_equals($validKey, $key)) {
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
