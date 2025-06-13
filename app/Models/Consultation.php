<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;
    
    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'client_id',
        'business_id',
        'advisory_id',
        'consultation_party',
        'title',
        'status',
        'status_detail',
        'case_summary',
        'special_notes',
        'inquirycontent',
        'firstchoice_datetime',
        'secondchoice_datetime',
        'inquirytype',
        'consultationtype',
        'case_category',
        'case_subcategory',
        'opponent_confliction',
        'consultation_receptiondate',
        'consultation_firstdate',
        'enddate',
        'consultation_notreason',
        'consultation_feedback',
        'reason_termination',
        'reason_termination_detail',
        'office_id',
        'lawyer_id',
        'lawyer2_id',
        'lawyer3_id',
        'paralegal_id',
        'paralegal2_id',
        'paralegal3_id',
        'feefinish_prospect',
        'feesystem',
        'sales_prospect',
        'feesystem_initialvalue',
        'sales_reason_updated',
        'enddate_prospect',
        'enddate_prospect_initialvalue',
        'route',
        'routedetail',
        'introducer',
        'introducer_others',
    ];

    protected $casts = [
        'firstchoice_datetime' => 'datetime',
        'secondchoice_datetime' => 'datetime',
    ];

    /**
     * クライアントとのリレーション（1対多の逆）
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
