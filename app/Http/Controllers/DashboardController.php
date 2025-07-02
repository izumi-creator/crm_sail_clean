<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Negotiation;
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

        $tasks = Task::where('worker_id', $userId)
            ->whereNotIn('status', [5, 6]) // 5:完了, 6:取下げ
            ->orderByDesc('created_at')
            ->get();

        $negotiations = Negotiation::where('worker_id', $userId)
            ->whereNotIn('status', [5, 6])
            ->orderByDesc('created_at')
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

        return view('dashboard', compact('tasks', 'negotiations', 'inquiries', 'consultations', 'businesses', 'advisoryContracts', 'advisoryConsultations'));
    }
}