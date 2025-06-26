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
        'advisory_consultation_id',
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
     * クライアントとのリレーション
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * 受任案件とのリレーション
     */
    public function business()
    {
        return $this->hasOne(Business::class);
    }
    /**
     * 相談案件とのリレーション
     */
    public function advisoryConsultation()
    {
        return $this->belongsTo(AdvisoryConsultation::class);
    }
    /**
     * 関係者とのリレーション
     */
    public function relatedParties()
    {
        return $this->hasMany(RelatedParty::class);
    }
    /**
     * タスクとのリレーション
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    /**
     * ユーザとのリレーション
     */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function lawyer2()
    {
        return $this->belongsTo(User::class, 'lawyer2_id');
    }

    public function lawyer3()
    {
        return $this->belongsTo(User::class, 'lawyer3_id');
    }

    public function paralegal()
    {
        return $this->belongsTo(User::class, 'paralegal_id');
    }

    public function paralegal2()
    {
        return $this->belongsTo(User::class, 'paralegal2_id');
    }

    public function paralegal3()
    {
        return $this->belongsTo(User::class, 'paralegal3_id');
    }

}
