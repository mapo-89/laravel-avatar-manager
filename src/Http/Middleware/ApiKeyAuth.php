<?php 

namespace Mapo89\LaravelAvatarManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-KEY');
        $validKeys = config('avatar-manager.api_keys', []);

        if (!$key || !in_array($key, $validKeys)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
