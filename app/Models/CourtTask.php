<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class CourtTask extends Model
{
    use HasFactory, UserTracking;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'court_id',
        'business_id',
        'status',
        'status_detail',
        'case_number',
        'department',
        'judge',
        'clerk',
        'tel_direct',
        'fax_direct',
        'email_direct',
        'task_category',
        'task_title',
        'task_content',
        'lawyer_id',
        'paralegal_id',
        'deadline',
        'move_time',
        'memo',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];
    /**
     * リレーション
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
    public function paralegal()
    {
        return $this->belongsTo(User::class, 'paralegal_id');
    }   
}