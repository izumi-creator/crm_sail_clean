<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Negotiation extends Model
{
    use HasFactory;
    
    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'related_party',
        'consultation_id',
        'business_id',
        'advisory_contract_id',
        'advisory_consultation_id',
        'task_id',
        'record1',
        'record2',
        'title',
        'record_date',
        'deadline_date',
        'deadline_time',
        'content',
        'orderer_id',
        'worker_id',
        'attachment1_title',
        'attachment2_title',
        'attachment3_title',
        'link1',
        'link2',
        'link3',
        'carrier',
        'tracking_number',
        'record_to',
        'phone_number',
        'phone_to',
        'phone_from',
        'naisen_to',
        'naisen_from',
    ];

    /**
     * ユーザとのリレーション
     */
    public function orderer()
    {
        return $this->belongsTo(User::class, 'orderer_id');
    }
    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    /**
     * 相談とのリレーション
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * 顧問契約とのリレーション
     */
    public function advisoryContract()
    {
        return $this->belongsTo(AdvisoryContract::class);
    }

    /**
     * 受任案件とのリレーション
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * 顧問相談とのリレーション
     */
    public function advisoryConsultation()
    {
        return $this->belongsTo(AdvisoryConsultation::class);
    }
    /**
     * タスク管理とのリレーション
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
