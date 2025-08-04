<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TaskComment;
use App\Models\Task;
use App\Models\Inquiry;
use App\Models\Consultation;
use App\Models\Business;
use App\Models\AdvisoryContract;
use App\Models\AdvisoryConsultation;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 未読コメント
        $unreadComments = TaskComment::with('task') // ← ココを追加
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('to_id', $userId)
                      ->where('already_read', false);
                })->orWhere(function ($q) use ($userId) {
                    $q->where('to2_id', $userId)
                      ->where('already_read2', false);
                })->orWhere(function ($q) use ($userId) {
                    $q->where('to3_id', $userId)
                      ->where('already_read3', false);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        // 📞 未完了電話タスク
        $phoneTasks = Task::where('worker_id', $userId)
            ->whereNotIn('status', [5, 6])
            ->where('record1', 1)
            ->orderByRaw('deadline_date IS NULL')
            ->orderBy('deadline_date')
            ->orderBy('status')
            ->get();

        // 📌 通常タスク（電話以外）
        $todoTasks = Task::where('worker_id', $userId)
            ->whereNotIn('status', [5, 6]) // 未完了
            ->where(function ($query) {
                $query->whereNull('record1')->orWhere('record1', '!=', 1); // 電話以外
            })
            ->orderByRaw('deadline_date IS NULL') // null最後
            ->orderBy('deadline_date')
            ->orderBy('status')
            ->get();
          

        $inquiries = Inquiry::where(function($q) use ($userId) {
            $q->where('manager_id', $userId)
              ->orWhereNull('manager_id');
        })
        ->where('status', 1) // 受付中
        ->orderByDesc('created_at')
        ->get();

        $consultations = Consultation::where(function($q) use ($userId) {
                $q->where('lawyer_id', $userId)
                  ->orWhere('lawyer2_id', $userId)
                  ->orWhere('lawyer3_id', $userId)
                  ->orWhere('paralegal_id', $userId)
                  ->orWhere('paralegal2_id', $userId)
                  ->orWhere('paralegal3_id', $userId);
            })
            ->whereNotIn('status', [4, 6])
            ->orderByDesc('created_at')
            ->get();

        $businesses = Business::where(function($q) use ($userId) {
                $q->where('lawyer_id', $userId)
                  ->orWhere('lawyer2_id', $userId)
                  ->orWhere('lawyer3_id', $userId)
                  ->orWhere('paralegal_id', $userId)
                  ->orWhere('paralegal2_id', $userId)
                  ->orWhere('paralegal3_id', $userId);
            })
            ->where('status', '!=', 4)
            ->orderByDesc('created_at')
            ->get();        

        $advisoryContracts = AdvisoryContract::where(function($q) use ($userId) {
                $q->where('lawyer_id', $userId)
                  ->orWhere('lawyer2_id', $userId)
                  ->orWhere('lawyer3_id', $userId)
                  ->orWhere('paralegal_id', $userId)
                  ->orWhere('paralegal2_id', $userId)
                  ->orWhere('paralegal3_id', $userId);
            })
            ->whereNotIn('status', [4, 6])
            ->orderByDesc('created_at')
            ->get();

        $advisoryConsultations = AdvisoryConsultation::where(function($q) use ($userId) {
            $q->where('lawyer_id', $userId)
              ->orWhere('lawyer2_id', $userId)
              ->orWhere('lawyer3_id', $userId)
              ->orWhere('paralegal_id', $userId)
              ->orWhere('paralegal2_id', $userId)
              ->orWhere('paralegal3_id', $userId);
        })
        ->whereNotIn('status', [3, 4])
        ->orderByDesc('created_at')
        ->get();

        return view('dashboard', compact('unreadComments', 'phoneTasks', 'todoTasks', 'inquiries', 'consultations', 'businesses', 'advisoryContracts', 'advisoryConsultations'));
    }
}