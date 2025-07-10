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
    
            // 🔽 利用不可ステータスのユーザーはログイン拒否
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

    }
}
