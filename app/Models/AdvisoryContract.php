<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class AdvisoryContract extends Model
{
    use HasFactory, UserTracking;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'client_id',
        'advisory_party',
        'title',
        'status',
        'opponent_confliction',
        'opponent_confliction_date',
        'explanation',
        'special_notes',
        'advisory_start_date',
        'advisory_end_date',
        'amount_monthly',
        'contract_term_monthly',
        'consultation_firstdate',
        'payment_category',
        'adviser_fee_auto',
        'payment_method',
        'withdrawal_request_amount',
        'withdrawal_breakdown',
        'withdrawal_update_date',
        'office_id',
        'lawyer_id',
        'lawyer2_id',
        'lawyer3_id',
        'paralegal_id',
        'paralegal2_id',
        'paralegal3_id',
        'source',
        'source_detail',
        'introducer_others',
        'gift',
        'newyearscard',
    ];

    /**
     * クライアントとのリレーション
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
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

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    /**
     * 顧問相談とのリレーション
     */
    public function advisoryConsultation()
    {
        return $this->hasMany(AdvisoryConsultation::class);
    }
    /**
     * タスクとのリレーション
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    /**
     * 折衝履歴とのリレーション
     */
    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }
}
