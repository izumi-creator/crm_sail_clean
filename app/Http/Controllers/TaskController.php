<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Task;
use App\Models\Consultation;
use App\Models\Business;
use App\Models\AdvisoryContract;
use App\Models\AdvisoryConsultation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\SlackBotNotificationService;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
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

    // タスク一覧画面
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        // worker名で検索
        if ($request->filled('worker_name')) {
            $workerIds = User::where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->worker_name . '%');
            })->pluck('id');

            $query->whereIn('worker_id', $workerIds);
        }
        if ($request->filled('deadline_date')) {
            $query->where('deadline_date', $request->deadline_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->with('worker')->paginate(15);
        return view('task.index', compact('tasks'));
    }

    // タスク登録画面
    public function create()
    {
        return view('task.create');
    }
    // タスク登録処理
    public function store(Request $request)
    {

        // ▼ Select2の初期テキスト表示対応（相談）
        if ($request->has('consultation_id')) {
            $consultation = Consultation::find($request->input('consultation_id'));
            if ($consultation) {
                $request->merge([
                    'consultation_name_display' => $consultation->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（受任案件）
        if ($request->has('business_id')) {
            $business = Business::find($request->input('business_id'));
            if ($business) {
                $request->merge([
                    'business_name_display' => $business->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（顧問契約）
        if ($request->has('advisory_contract_id')) {
            $advisoryContract = AdvisoryContract::find($request->input('advisory_contract_id'));
            if ($advisoryContract) {
                $request->merge([
                    'advisory_contract_name_display' => $advisoryContract->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（顧問相談）
        if ($request->has('advisory_consultation_id')) {
            $advisoryConsultation = AdvisoryConsultation::find($request->input('advisory_consultation_id'));
            if ($advisoryConsultation) {
                $request->merge([
                    'advisory_consultation_name_display' => $advisoryConsultation->title,
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'related_party' => 'required|in:' . implode(',', array_keys(config('master.related_parties'))),
            'consultation_id' => 'nullable|exists:consultations,id',
            'business_id' => 'nullable|exists:businesses,id',
            'advisory_contract_id' => 'nullable|exists:advisory_contracts,id',
            'advisory_consultation_id' => 'nullable|exists:advisory_consultations,id',
            'record1' => 'required|in:' . implode(',', array_keys(config('master.records_1'))),
            'record2' => 'required|in:' . implode(',', array_keys(config('master.records_2'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.task_statuses'))),
            'already_read' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'record_date' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'content' => 'nullable|string|max:10000',
            'orderer_id' => 'nullable|exists:users,id',
            'worker_id' => 'required|exists:users,id',
            'attachment1_title' => 'nullable|string|max:255',
            'attachment2_title' => 'nullable|string|max:255',
            'attachment3_title' => 'nullable|string|max:255',
            'link1' => 'nullable|url',
            'link2' => 'nullable|url',
            'link3' => 'nullable|url',
            'carrier' => 'nullable|in:' . implode(',', array_keys(config('master.carriers'))),
            'tracking_number' => 'nullable|string|max:255',
            'phone_request' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'notify_type' => 'nullable|in:' . implode(',', array_keys(config('master.notify_types'))),
            'record_to' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'notify_person_in' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'memo' => 'nullable|string|max:100000',
        ]);

        $validator->after(function ($validator) use ($request) {
            switch ((int)$request->related_party) {
                case 1:
                    if (empty($request->consultation_id)) {
                        $validator->errors()->add('consultation_id', '「相談」を入力してください。');
                    }
                    break;
                case 2:
                    if (empty($request->business_id)) {
                        $validator->errors()->add('business_id', '「受任案件」を入力してください。');
                    }
                    break;
                case 3:
                    if (empty($request->advisory_contract_id)) {
                        $validator->errors()->add('advisory_contract_id', '「顧問契約」を入力してください。');
                    }
                    break;
                case 4:
                    if (empty($request->advisory_consultation_id)) {
                        $validator->errors()->add('advisory_consultation_id', '「顧問相談」を入力してください。');
                    }
                    break;
            }
        });

        $validated = $validator->validate();

        $task = Task::create([
            'related_party' => $validated['related_party'],
            'consultation_id' => $validated['consultation_id'],
            'business_id' => $validated['business_id'],
            'advisory_contract_id' => $validated['advisory_contract_id'],
            'advisory_consultation_id' => $validated['advisory_consultation_id'],
            'record1' => $validated['record1'],
            'record2' => $validated['record2'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'already_read' => $validated['already_read'] ?? '0',
            'record_date' => $validated['record_date'],
            'deadline_date' => $validated['deadline_date'],
            'deadline_time' => $validated['deadline_time'],
            'content' => $validated['content'],
            'orderer_id' => $validated['orderer_id'],
            'worker_id' => $validated['worker_id'],
            'attachment1_title' => $validated['attachment1_title'],
            'attachment2_title' => $validated['attachment2_title'],
            'attachment3_title' => $validated['attachment3_title'],
            'link1' => $validated['link1'],
            'link2' => $validated['link2'],
            'link3' => $validated['link3'],
            'carrier' => $validated['carrier'],
            'tracking_number' => $validated['tracking_number'],
            'phone_request' => $validated['phone_request'] ?? '0',
            'notify_type' => $validated['notify_type'],
            'record_to' => $validated['record_to'],
            'phone_number' => $validated['phone_number'],
            'phone_to' => $validated['phone_to'],
            'phone_from' => $validated['phone_from'],
            'naisen_to' => $validated['naisen_to'],
            'naisen_from' => $validated['naisen_from'],
            'notify_person_in' => $validated['notify_person_in'] ?? '0',
            'memo' => $validated['memo'] ?? '',
        ]);


        // ✅ Slack通知送信
        $notifiedUserIds = collect([
            optional($task->orderer)->id,
            optional($task->worker)->id,
        ]);

        switch ($task->related_party) {
            case 1:
                $related = $task->consultation;
                break;
            case 2:
                $related = $task->business;
                break;
            case 3:
                $related = $task->advisoryContract;
                break;
            case 4:
                $related = $task->advisoryConsultation;
                break;
            default:
                $related = null;
        }

        if ($related) {
            $notifiedUserIds = $notifiedUserIds->merge([
                optional($related->lawyer)->id,
                optional($related->lawyer2)->id,
                optional($related->lawyer3)->id,
                optional($related->paralegal)->id,
                optional($related->paralegal2)->id,
                optional($related->paralegal3)->id,
            ]);
        }

        $creatorName = optional($task->createdByUser)->name;
        $url = route('task.show', ['task' => $task->id]);


        // リレーション取得
        switch ($task->related_party) {
            case 1: $related = $task->consultation; break;
            case 2: $related = $task->business; break;
            case 3: $related = $task->advisoryContract; break;
            case 4: $related = $task->advisoryConsultation; break;
            default: $related = null;
        }

        // 関連先名と件名
        $relatedTypeName = config('master.related_parties')[$task->related_party] ?? '不明';
        $relatedTitle = $related->title ?? '';
        $relatedDisplay = "関連先：{$relatedTypeName}　{$relatedTitle}";
        $ordererName = optional($task->orderer)->name ?? '（なし）';
        $workerName = optional($task->worker)->name ?? '（なし）';
        $userDisplay = "依頼者：{$ordererName}\n担当者：{$workerName}";    
        
        $notifiedUsers = User::whereIn('id', $notifiedUserIds->filter()->unique())->get();

        $message = "📌 タスクを登録しました。\nタスクの件名：{$task->title}\n{$userDisplay}\n{$relatedDisplay}\n登録者：{$creatorName}\n🔗 URL：{$url}";

        $slackBot = app(SlackBotNotificationService::class);
        foreach ($notifiedUsers as $user) {
            if (!empty($user->slack_channel_id)) {
                $slackBot->sendMessage($message, $user->slack_channel_id);
            }
        }

        if ($request->filled('redirect_url')) {
        return redirect($request->input('redirect_url'))->with('success', 'タスクを登録しました！');
        }
        
        return redirect()->route('task.index')->with('success', 'タスクを登録しました！');
    }

    // タスク詳細処理
    public function show(Task $task)
    {
        // 関連データをロード
        $task->load([
            'consultation',
            'business',
            'advisoryContract',
            'advisoryConsultation',
            'orderer',
            'worker',
        ]);

        return view('task.show', compact('task'));
    }

    // タスク編集画面
    public function update(Request $request, Task $task)
    {

        $before_status = $task->status;

        $validator = Validator::make($request->all(), [
            'record1' => 'required|in:' . implode(',', array_keys(config('master.records_1'))),
            'record2' => 'required|in:' . implode(',', array_keys(config('master.records_2'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.task_statuses'))),
            'already_read' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'record_date' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'content' => 'nullable|string|max:10000',
            'orderer_id' => 'nullable|exists:users,id',
            'worker_id' => 'required|exists:users,id',
            'attachment1_title' => 'nullable|string|max:255',
            'attachment2_title' => 'nullable|string|max:255',
            'attachment3_title' => 'nullable|string|max:255',
            'link1' => 'nullable|url',
            'link2' => 'nullable|url',
            'link3' => 'nullable|url',
            'carrier' => 'nullable|in:' . implode(',', array_keys(config('master.carriers'))),
            'tracking_number' => 'nullable|string|max:255',
            'phone_request' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'notify_type' => 'nullable|in:' . implode(',', array_keys(config('master.notify_types'))),
            'record_to' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'notify_person_in' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'memo' => 'nullable|string|max:100000',
        ]);

        $validated = $validator->validate();

        $task->update([
            'record1' => $validated['record1'],
            'record2' => $validated['record2'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'already_read' => $validated['already_read'] ?? '0',
            'record_date' => $validated['record_date'],
            'deadline_date' => $validated['deadline_date'],
            'deadline_time' => $validated['deadline_time'],
            'content' => $validated['content'],
            'orderer_id' => $validated['orderer_id'],
            'worker_id' => $validated['worker_id'],
            'attachment1_title' => $validated['attachment1_title'],
            'attachment2_title' => $validated['attachment2_title'],
            'attachment3_title' => $validated['attachment3_title'],
            'link1' => $validated['link1'],
            'link2' => $validated['link2'],
            'link3' => $validated['link3'],
            'carrier' => $validated['carrier'],
            'tracking_number' => $validated['tracking_number'],
            'phone_request' => $validated['phone_request'] ?? '0',
            'notify_type' => $validated['notify_type'],
            'record_to' => $validated['record_to'],
            'phone_number' => $validated['phone_number'],
            'phone_to' => $validated['phone_to'],
            'phone_from' => $validated['phone_from'],
            'naisen_to' => $validated['naisen_to'],
            'naisen_from' => $validated['naisen_from'],
            'notify_person_in' => $validated['notify_person_in'] ?? '0',
            'memo' => $validated['memo'] ?? '',
        ]);

        // ✅ Slack通知送信
        $before_status = (int) $before_status;
        $after_status = (int) $validated['status'];


        if ($before_status !== $after_status) {
            $statusLabels = config('master.task_statuses');


            // ✅ Slack通知内容
            $beforeLabel = $statusLabels[$before_status] ?? "不明（$before_status）";
            $afterLabel = $statusLabels[$after_status] ?? "不明（$after_status）";
            $updaterName = optional($task->updatedByUser)->name ?? '不明';
            $url = route('task.show', ['task' => $task->id]);

            $notifiedUserIds = collect([
                optional($task->orderer)->id,
                optional($task->worker)->id,
            ]);

            switch ($task->related_party) {
                case 1:
                    $related = $task->consultation;
                    break;
                case 2:
                    $related = $task->business;
                    break;
                case 3:
                    $related = $task->advisoryContract;
                    break;
                case 4:
                    $related = $task->advisoryConsultation;
                    break;
                default:
                    $related = null;
            }

            if ($related) {
                $notifiedUserIds = $notifiedUserIds->merge([
                    optional($related->lawyer)->id,
                    optional($related->lawyer2)->id,
                    optional($related->lawyer3)->id,
                    optional($related->paralegal)->id,
                    optional($related->paralegal2)->id,
                    optional($related->paralegal3)->id,
                ]);
            }

            // リレーション取得
            switch ($task->related_party) {
                case 1: $related = $task->consultation; break;
                case 2: $related = $task->business; break;
                case 3: $related = $task->advisoryContract; break;
                case 4: $related = $task->advisoryConsultation; break;
                default: $related = null;
            }

            // 関連先名と件名
            $relatedTypeName = config('master.related_parties')[$task->related_party] ?? '不明';
            $relatedTitle = $related->title ?? '';
            $relatedDisplay = "関連先：{$relatedTypeName}　{$relatedTitle}";
            $ordererName = optional($task->orderer)->name ?? '（なし）';
            $workerName = optional($task->worker)->name ?? '（なし）';
            $userDisplay = "依頼者：{$ordererName}\n担当者：{$workerName}";    

            $notifiedUsers = User::whereIn('id', $notifiedUserIds->filter()->unique())->get();


            $message = "🗑️ タスクをステータスが変更されました\nタスクの件名：{$task->title}\nステータス：{$beforeLabel} → {$afterLabel}\n{$userDisplay}\n{$relatedDisplay}\n更新者：{$updaterName}\n🔗 URL：{$url}";

            // Slack通知送信
            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($message, $user->slack_channel_id);
                }
            }
        }
        
        return redirect()->route('task.show', $task)->with('success', 'タスクを更新しました！');
    }
    
    // タスク削除画面
    public function destroy(Task $task)
    {
        $this->ensureIsAdmin();

        try {

            // ✅ Slack通知内容
            $notifiedUserIds = collect([
                optional($task->orderer)->id,
                optional($task->worker)->id,
            ]);

            switch ($task->related_party) {
                case 1:
                    $related = $task->consultation;
                    break;
                case 2:
                    $related = $task->business;
                    break;
                case 3:
                    $related = $task->advisoryContract;
                    break;
                case 4:
                    $related = $task->advisoryConsultation;
                    break;
                default:
                    $related = null;
            }

            if ($related) {
                $notifiedUserIds = $notifiedUserIds->merge([
                    optional($related->lawyer)->id,
                    optional($related->lawyer2)->id,
                    optional($related->lawyer3)->id,
                    optional($related->paralegal)->id,
                    optional($related->paralegal2)->id,
                    optional($related->paralegal3)->id,
                ]);
            }

            // リレーション取得
            switch ($task->related_party) {
                case 1: $related = $task->consultation; break;
                case 2: $related = $task->business; break;
                case 3: $related = $task->advisoryContract; break;
                case 4: $related = $task->advisoryConsultation; break;
                default: $related = null;
            }

            // 関連先名と件名
            $relatedTypeName = config('master.related_parties')[$task->related_party] ?? '不明';
            $relatedTitle = $related->title ?? '';
            $relatedDisplay = "関連先：{$relatedTypeName}　{$relatedTitle}";
            $ordererName = optional($task->orderer)->name ?? '（なし）';
            $workerName = optional($task->worker)->name ?? '（なし）';
            $userDisplay = "依頼者：{$ordererName}\n担当者：{$workerName}";    

            $notifiedUsers = User::whereIn('id', $notifiedUserIds->filter()->unique())->get();

            // タスク削除
            $task->delete();

            // 削除者の名前を取得
            $userName = Auth::user()?->name ?? '不明';

            $message = "🗑️ タスクを削除しました。\nタスクの件名：{$task->title}\n{$userDisplay}\n{$relatedDisplay}\n削除者：{$userName}";

            // Slack通知送信
            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($message, $user->slack_channel_id);
                }
            }

            return redirect()->route('task.index')->with('success', '削除しました');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                return response()->view('errors.db_constraint', [
                    'message' => '関連データがあるため削除できません。'
                ], 500);
            }
        
            // 1451以外のエラーはLaravelの例外処理に投げる
            throw $e;
        }        
    }

}