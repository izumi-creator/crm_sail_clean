<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */

    protected $fillable = [
        'client_id',
        'consultation_id',
        'advisory_id',
        'consultation_party',
        'title',
        'status',
        'status_detail',
        'case_summary',
        'special_notes',
        'inquirytype',
        'consultationtype',
        'case_category',
        'case_subcategory',
        'appointment_date',
        'close_date',
        'close_notreason',
        'status_limitday',
        'office_id',
        'lawyer_id',
        'lawyer2_id',
        'lawyer3_id',
        'paralegal_id',
        'paralegal2_id',
        'paralegal3_id',
        'duedate_memo',
        'feefinish_prospect',
        'feesystem',
        'sales_prospect',
        'feesystem_initialvalue',
        'sales_reason_updated',
        'enddate_prospect',
        'enddate_prospect_initialvalue',
        'delay_check',
        'deposit',
        'performance_reward',
        'difference',
        'requestfee_initialvalue',
        'requestfee',
        'requestfee_balance',
        'childsupport_collect',
        'childsupport_phase',
        'childsupport_monthly_fee',
        'childsupport_monthly_remuneration',
        'childsupport_notcollected_amount',
        'childsupport_remittance_amount',
        'childsupport_payment_date',
        'childsupport_start_payment',
        'childsupport_end_payment',
        'childsupport_deposit_account',
        'childsupport_deposit_date',
        'childsupport_transfersource_name',
        'childsupport_repayment_date',
        'childsupport_financialinstitution_name',
        'childsupport_refundaccount_name',
        'childsupport_temporary_payment',
        'childsupport_memo',
        'route',
        'routedetail',
        'introducer',
        'introducer_others',
        'comment',
        'progress_comment',
    ];

    /**
     * クライアントとのリレーション
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * 相談とのリレーション
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
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
    /**
     * 関係者とのリレーション
     */
    public function relatedParties()
    {
        return $this->hasMany(RelatedParty::class);
    }
    /**
     * 裁判所対応とのリレーション
     */
    public function courtTasks()
    {
        return $this->hasMany(CourtTask::class);
    }
}