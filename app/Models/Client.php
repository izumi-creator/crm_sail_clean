<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'client_type',
        'name_kanji',
        'name_kana',
        'name_abbreviation',
        'phone_number',
        'phone_number2',
        'mobile_number',
        'home_phone_number',
        'not_home_contact',
        'first_contact_number',
        'second_contact_number',
        'fax',
        'email1',
        'email2',
        'address_postalcode',
        'address_state',
        'address_city',
        'address_street',
        'address_name_kanji',
        'address_name_kana',
        'contact_postalcode',
        'contact_state',
        'contact_city',
        'contact_street',
        'contact_name_kanji',
        'contact_name_kana',
        'contact_address_notes',
        'last_name_kanji',
        'first_name_kanji',
        'last_name_kana',
        'first_name_kana',
        'birthday',
        'identification_document1',
        'identification_document2',
        'identification_document3',
        'send_newyearscard',
        'send_summergreetingcard',
        'send_office_news',
        'send_autocreation',
        'contact_last_name_kanji',
        'contact_first_name_kanji',
        'contact_last_name_kana',
        'contact_first_name_kana',
        'contact_phone_number',
        'contact_mobile_number',
        'contact_home_phone_number',
        'contact_email1',
        'contact_email2',
        'contact_fax',
    ];
}
