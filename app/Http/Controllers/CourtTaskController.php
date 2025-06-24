<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourtTask;
use App\Models\Court;
use App\Models\Business;
use App\Models\User;
use Illuminate\Validation\Rule;

class CourtTaskController extends Controller
{

    /**
     * 管理者権限チェック
     */
    private function ensureIsAdmin()
    {
        $loginUser = auth()->user();
        if ($loginUser->role_type != 1) {
            abort(403, '管理者権限が必要です。');
        }
    }

    // 裁判所対応追加画面
    public function create(Business $business)
    {
        return view('court_task.create', compact('business'));
    }
    // 裁判所対応追加処理
    public function store(Request $request)
    {

        // ▼ Select2の初期テキスト表示対応（受任案件）
        if ($request->has('business_id')) {
            $business = Business::find($request->input('business_id'));
            if ($business) {
                $request->merge([
                    'business_name_display' => $business->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（裁判所）
        if ($request->has('court_id')) {
            $court = Court::find($request->input('court_id'));
            if ($court) {
                $request->merge([
                    'court_name_display' => $court->court_name,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（弁護士）
        if ($request->has('lawyer_id')) {
            $lawyer = User::find($request->input('lawyer_id'));
            if ($lawyer) {
                $request->merge([
                    'lawyer_name_display' => $lawyer->name,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（パラリーガル）
        if ($request->has('paralegal_id')) {
            $paralegal = User::find($request->input('paralegal_id'));
            if ($paralegal) {
                $request->merge([
                    'paralegal_name_display' => $paralegal->name,
                ]);
            }
        }

        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'business_id' => 'required|exists:businesses,id',
            'status' => 'required|in:' . implode(',', array_keys(config('master.court_tasks_statuses'))),
            'status_detail' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'judge' => 'nullable|string|max:255',
            'clerk' => 'nullable|string|max:255',
            'tel_direct' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'fax_direct' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email_direct' => 'nullable|email|max:100',
            'task_category' => 'required|in:' . implode(',', array_keys(config('master.court_task_categories'))),
            'task_title' => 'required|string|max:255',
            'task_content' => 'nullable|string|max:1000',
            'lawyer_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'move_time' => 'nullable|date_format:H:i',
            'memo' => 'nullable|string|max:1000',
        ]);

        $deadline = null;
        if ($request->filled('deadline_date') && $request->filled('deadline_time')) {
            $deadline = \Carbon\Carbon::parse($request->deadline_date . ' ' . $request->deadline_time);
        }

        CourtTask::create([
            'client_id' => $request->client_id,
            'consultation_id' => $request->consultation_id,
            'business_id' => $request->business_id,
            'advisory_id' => $request->advisory_id,
            'court_id' => $request->court_id,
            'status' => $request->status,
            'status_detail' => $request->status_detail,
            'department' => $request->department,
            'judge' => $request->judge,
            'clerk' => $request->clerk,
            'tel_direct' => $request->tel_direct,
            'fax_direct' => $request->fax_direct,
            'email_direct' => $request->email_direct,
            'task_category' => $request->task_category,
            'task_title' => $request->task_title,
            'task_content' => $request->task_content,
            'lawyer_id' => $request->lawyer_id,
            'paralegal_id' => $request->paralegal_id,
            'deadline' => $deadline,
            'move_time' => $request->move_time,
            'memo' => $request->memo,
        ]);
        // 裁判所対応の登録後、受任案件の裁判所対応タブにリダイレクト
        return redirect(
            url()->route('business.show', ['business' => $request->business_id]) . '#tab-courtTask'
            )->with('success', '裁判所対応を追加しました！');
        }

    // 裁判所対応詳細画面
    public function show(CourtTask $court_task)
    {
        $court_task->load(['business', 'court', 'lawyer', 'paralegal']);

        return view('court_task.show', compact('court_task'));
    }

    // 裁判所対応更新処理
    public function update(Request $request, CourtTask $court_task)
    {
        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'business_id' => 'required|exists:businesses,id',
            'status' => 'required|in:' . implode(',', array_keys(config('master.court_tasks_statuses'))),
            'status_detail' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'judge' => 'nullable|string|max:255',
            'clerk' => 'nullable|string|max:255',
            'tel_direct' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'fax_direct' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email_direct' => 'nullable|email|max:100',
            'task_category' => 'required|in:' . implode(',', array_keys(config('master.court_task_categories'))),
            'task_title' => 'required|string|max:255',
            'task_content' => 'nullable|string|max:1000',
            'lawyer_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'move_time' => 'nullable|date_format:H:i',
            'memo' => 'nullable|string|max:1000',
        ]);

        $deadline = null;
        if ($request->filled('deadline_date') && $request->filled('deadline_time')) {
            $deadline = \Carbon\Carbon::parse($request->deadline_date . ' ' . $request->deadline_time);
        }

        $court_task->update([
            'court_id' => $request->court_id,
            'business_id' => $request->business_id,
            'status' => $request->status,
            'status_detail' => $request->status_detail,
            'department' => $request->department,
            'judge' => $request->judge,
            'clerk' => $request->clerk,
            'tel_direct' => $request->tel_direct,
            'fax_direct' => $request->fax_direct,
            'email_direct' => $request->email_direct,
            'task_category' => $request->task_category,
            'task_title' => $request->task_title,
            'task_content' => $request->task_content,
            'lawyer_id' => $request->lawyer_id,
            'paralegal_id' => $request->paralegal_id,
            'deadline' => $deadline,
            'move_time' => $request->move_time,
            'memo' => $request->memo,
        ]);

        return redirect()->route('court_task.show', $court_task)->with('success', '裁判所対応を更新しました！');
    }

    // 裁判所対応削除処理
    public function destroy(CourtTask $court_task)
    {
        $this->ensureIsAdmin(); // 管理者権限チェック
        $court_task->delete();
        return redirect(
            url()->route('business.show', ['business' => $court_task->business_id]) . '#tab-courtTask'
            )->with('success', '裁判所対応を削除しました！');
    }        
}