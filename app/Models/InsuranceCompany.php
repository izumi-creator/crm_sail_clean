<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class InsuranceCompany extends Model
{
    use HasFactory, UserTracking;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'insurance_name',
        'insurance_type',
        'contactname',
        'phone_number',
        'email',
        'contactname2',
        'phone_number2',
        'email2',
        'importantnotes',
    ];
}
