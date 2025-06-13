<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    
    // 各項目に書いている @error を共通コンポーネント化
    Blade::directive('errorText', function ($field) {
        return "<?php if (\$errors->has($field)): ?>
                    <p class=\"text-red-500 text-xs mt-1\"><?php echo e(\$errors->first($field)); ?></p>
                <?php endif; ?>";
    });

    // パスワードの強度チェック
    // 8文字以上、英大文字・小文字・数字・記号のうち3種類以上
    Validator::extend('password_strength', function ($attribute, $value, $parameters, $validator) {
        $hasUppercase = preg_match('/[A-Z]/', $value) === 1;
        $hasLowercase = preg_match('/[a-z]/', $value) === 1;
        $hasDigit     = preg_match('/[0-9]/', $value) === 1;
        $hasSymbol    = preg_match('/[^A-Za-z0-9]/', $value) === 1;
    
        $typesUsed = ($hasUppercase ? 1 : 0)
                   + ($hasLowercase ? 1 : 0)
                   + ($hasDigit     ? 1 : 0)
                   + ($hasSymbol    ? 1 : 0);
    
        return strlen($value) >= 8 && $typesUsed >= 3;
    });
    
    }
}
