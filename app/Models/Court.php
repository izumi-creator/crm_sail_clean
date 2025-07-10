<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class Court extends Model
{
    use HasFactory, UserTracking;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'court_name',
        'court_type',
        'postal_code',
        'location',
        'phone_number',
        'importantnotes',
    ];

    /**
     * 裁判所対応とのリレーション
     */
    public function relatedParties()
    {
        return $this->hasMany(RelatedParty::class);
    }
}
