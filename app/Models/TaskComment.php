<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class TaskComment extends Model
{
    use HasFactory, UserTracking;

    /**
     * 一括代入可能な属性（フォームからの保存対象）
     */
    protected $fillable = [
        'task_id',
        'comment',
        'from_id',
        'to_id',
        'to2_id',
        'to3_id',
        'already_read',
        'already_read2',
        'already_read3',
    ];

    protected $casts = [
        'already_read' => 'boolean',
        'already_read2' => 'boolean',
        'already_read3' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function from()
    {
        return $this->belongsTo(User::class, 'from_id');
    }
    public function to()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
    public function to2()
    {
        return $this->belongsTo(User::class, 'to2_id');
    }
    public function to3()
    {
        return $this->belongsTo(User::class, 'to3_id');
    }
}