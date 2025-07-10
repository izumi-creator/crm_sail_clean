<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class AdvisoryConsultation extends Model
{
    use HasFactory, UserTracking;

        /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'client_id',
        'advisory_contract_id',
        'consultation_id',
        'advisory_party',
        'title',
        'status',
        'opponent_confliction',
        'opponent_confliction_date',
        'case_summary',
        'special_notes',
        'consultation_start_date',
        'consultation_end_date',
        'close_reason',
        'office_id',
        'lawyer_id',
        'lawyer2_id',
        'lawyer3_id',
        'paralegal_id',
        'paralegal2_id',
        'paralegal3_id'
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
    /**
     * 顧問契約とのリレーション
     */
    public function advisoryContract()
    {
        return $this->belongsTo(AdvisoryContract::class);
    }
    /**
     * 相談とのリレーション
     */
    public function consultation()
    {
        return $this->hasOne(Consultation::class, 'advisory_consultation_id');
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
     * 折衝履歴とのリレーション
     */
    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }
}
