<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request; // ✅ 追加
use Illuminate\Support\Facades\View;

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
    
        Fortify::authenticateUsing(function ($request) {
    
            $user = User::where('user_id', $request->user_id)->first();
    
            if (!$user) {
                return null;
            }
    
            if (!Hash::check($request->password, $user->password)) {
                return null;
            }
    
            return $user;
        });

    }
}
