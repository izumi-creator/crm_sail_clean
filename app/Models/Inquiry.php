<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'status',
        'receptiondate',
        'inquiries_name_kanji',
        'inquiries_name_kana',
        'last_name_kanji',
        'first_name_kanji',
        'last_name_kana',
        'first_name_kana',
        'corporate_name',
        'phone_number',
        'email',
        'state',
        'firstchoice_datetime',
        'secondchoice_datetime',
        'inquirycontent',
        'route',
        'routedetail',
        'averageovertimehoursperweek',
        'monthlyincome',
        'lengthofservice',
        'consultation_id',
    ];

    protected $casts = [
    'receptiondate' => 'datetime',
    'firstchoice_datetime' => 'datetime',
    'secondchoice_datetime' => 'datetime',
    ];
}
