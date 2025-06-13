<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $table = 'relatedparties';

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'room_name',
        'calendar_id',
        'office_id',
        'importantnotes',
    ];
}
