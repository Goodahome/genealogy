<?php

namespace App\Http\Middleware;

use App\Models\ShareToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RestrictShareAccess
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        Log::info('RestrictShareAccess: Processing request', [
            'url' => $request->url(),
            'guards' => $guards,
            'token' => $request->route('token'),
        ]);

        // 如果没有传递守卫，默认使用 'web' 和 'sanctum'
        if (empty($guards)) {
            $guards = ['web', 'sanctum'];
        }

        // 检查用户是否已认证
        foreach ($guards as $guard) {
            try {
                if (Auth::guard($guard)->check()) {
                    Log::info('RestrictShareAccess: User authenticated', ['guard' => $guard]);
                    return $next($request);
                }
            } catch (\InvalidArgumentException $e) {
                Log::warning("RestrictShareAccess: Invalid auth guard: {$guard}", ['exception' => $e]);
                continue;
            }
        }

        // 未登录用户，验证分享 token
        $token = $request->route('token');
        $shareToken = ShareToken::where('token', $token)->first();

        if (!$shareToken) {
            Log::warning('RestrictShareAccess: Invalid or missing token', ['token' => $token]);
            abort(403, __('app.link_expired'));
        }

        if (!$shareToken->isValid()) {
            Log::warning('RestrictShareAccess: Token expired or invalid', ['token' => $token]);
            abort(403, __('app.link_expired'));
        }

        // 设置 shared_person_id
        $request->attributes->set('shared_person_id', $shareToken->person_id);
        Log::info('RestrictShareAccess: Token valid, shared_person_id set', [
            'shared_person_id' => $shareToken->person_id,
        ]);

        // 允许的路由
        $allowedRoutes = [
            'share.edit',
            'share.update',
            'people.edit-profile',
            'people.edit-contact',
            'people.edit-death',
            'people.edit-family',
            'people.edit-files',
            'people.edit-photos',
            'people.edit-partner',
            'people.update',
        ];

        $routeName = $request->route()->getName();
        if (!in_array($routeName, $allowedRoutes)) {
            Log::warning('RestrictShareAccess: Unauthorized route access', ['route' => $routeName]);
            abort(403, __('app.unauthorized_access'));
        }

        // 确保访问的人物 ID 与分享的人物一致
        $personId = $request->route('person')?->id;
        if ($personId && $personId != $shareToken->person_id) {
            Log::warning('RestrictShareAccess: Unauthorized person access', [
                'person_id' => $personId,
                'shared_person_id' => $shareToken->person_id,
            ]);
            abort(403, __('app.unauthorized_person'));
        }

        return $next($request);
    }
}