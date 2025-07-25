<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request; // ✅ 追加
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Illuminate\Contracts\Support\Responsable;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 🚨 `LoginResponse` をカスタムしていない場合は削除OK！
        // $this->app->singleton(
        //     \Laravel\Fortify\Contracts\LoginResponse::class,
        //     \App\Http\Responses\LoginResponse::class
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });
    
        Fortify::username(function () {
            return 'user_id';
        });
    
        Fortify::authenticateUsing(function ($request) {
            $user = User::where('user_id', $request->user_id)->first();
        
            if (!$user) {
                return null;
            }
        
            if (!Hash::check($request->password, $user->password)) {
                return null;
            }
        
            if (in_array($user->user_status, [2, 3, 4])) {
                throw ValidationException::withMessages([
                    Fortify::username() => match ($user->user_status) {
                        2 => 'このアカウントは退職済のため、ログインできません。',
                        3 => 'このアカウントは休職中のため、ログインできません。',
                        4 => 'このアカウントは現在利用できません。',
                        default => 'このアカウントはログインできません。',
                    }
                ]);
            }
        
            return $user;
        });
        
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });        
    
        $this->app->singleton(
            TwoFactorChallengeViewResponse::class,
            fn () => new class implements TwoFactorChallengeViewResponse {
                public function toResponse($request)
                {
                    return view('auth.two-factor-challenge');
                }
            }
        );

        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect()->route('dashboard'); // ✅ ダッシュボードにリダイレクト
                }
            };
        });
    }
}
