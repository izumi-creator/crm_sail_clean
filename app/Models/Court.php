<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Court extends Model
{
    use HasFactory;

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
}
