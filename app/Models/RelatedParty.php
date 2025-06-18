<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelatedParty extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'client_id',
        'consultation_id',
        'business_id',
        'advisory_id',
        'relatedparties_party',
        'relatedparties_class',
        'relatedparties_type',
        'relatedparties_position',
        'relatedparties_position_details',
        'relatedparties_explanation',
        'relatedparties_name_kanji',
        'relatedparties_name_kana',
        'mobile_number',
        'phone_number',
        'phone_number2',
        'fax',
        'email',
        'email2',
        'relatedparties_postcode',
        'relatedparties_address',
        'relatedparties_address2',
        'placeofwork',
        'manager_name_kanji',
        'manager_name_kana',
        'manager_post',
        'manager_department',
    ];

    /**
     * リレーション
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
