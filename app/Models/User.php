<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserTracking;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UserTracking;
    use TwoFactorAuthenticatable; // 2FA対応

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'email2',
        'password',
        'employee_type',
        'role_type',
        'user_status',
        'office_id',
        'phone_number',
        'phone_number2',
        'slack_channel_id',
    ];

    /**
     * JSONなどで非表示にする属性（APIレスポンス対策など）
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 自動キャストする属性（取得/保存時の変換）
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 10以降で自動Hash化される
        ];
    }

    /**
     * カスタムアクセサ（例：フルネームを取得）
     * - 利用例：$user->full_name
     */
    public function getFullNameAttribute()
    {
        return $this->name; // 氏名を full_name で呼びたい場合
    }

    /**
     * カスタムミューテタ（passwordを手動でHashする場合はこちら）
     */
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = Hash::make($value);
    // }
}
