<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorIsEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // ✅ 2FA未設定 または 未確認の場合はリダイレクト
            $needsTwoFactor = ! $user->two_factor_secret || is_null($user->two_factor_confirmed_at);

            if ($needsTwoFactor && ! $request->is('users/two-factor')) {
                return redirect()->route('users.two-factor')->with('warning', '二要素認証の設定を完了してください。');
            }
        }

        return $next($request);
    }
}