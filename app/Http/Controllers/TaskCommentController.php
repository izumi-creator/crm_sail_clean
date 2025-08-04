<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\SlackBotNotificationService;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:255|not_regex:/^\s*$/',
            'to_id' => 'nullable|exists:users,id',
            'to2_id' => 'nullable|exists:users,id',
            'to3_id' => 'nullable|exists:users,id',
        ]);
    
        $comment = new TaskComment();
        $comment->task_id = $task->id;
        $comment->comment = $request->comment;
        $comment->from_id = Auth::id();
        $comment->to_id = $request->to_id;
        $comment->to2_id = $request->to2_id;
        $comment->to3_id = $request->to3_id;
    
        $comment->save();
    
        // ▼ Slack通知処理（削除と統一）
        $fromUser = Auth::user();
    
        $notifiedUserIds = collect([
            $comment->from_id,
            $comment->to_id,
            $comment->to2_id,
            $comment->to3_id,
        ])->filter()->unique();
        
        $notifiedUsers = User::whereIn('id', $notifiedUserIds)->get();
        
        $taskTitle = $task->title;
        $url = route('task.show', ['task' => $task->id]);
        
        $message = "📝 タスク「{$taskTitle}」にコメントが投稿されました\n"
                 . "投稿者：{$fromUser->name}\n"
                 . "内容：{$comment->comment}\n"
                 . "🔗 URL：{$url}";
        
        $slackBot = app(SlackBotNotificationService::class);
        foreach ($notifiedUsers as $toUser) {
            if (!empty($toUser->slack_channel_id)) {
                $slackBot->sendMessage($message, $toUser->slack_channel_id);
            }
        }
    
        return redirect()->route('task.show', $task->id)->with('success', 'コメントを投稿しました！');
    }

    public function destroy(Task $task, TaskComment $comment)
    {
        if ($comment->from_id !== Auth::id()) {
            abort(403, 'このコメントを削除する権限がありません。');
        }

        $comment->delete();
        return redirect()->route('task.show', $task->id)
            ->with('success', 'コメントを削除しました。');
    }

    public function read(Request $request, Task $task, TaskComment $comment)
    {
        $request->validate([
            'recipient' => 'required|in:to_id,to2_id,to3_id',
        ]);

        $loginId = Auth::id();
        $recipient = $request->input('recipient');

        // 宛先が自分自身かチェック
        $isValid = match ($recipient) {
            'to_id'   => $comment->to_id === $loginId,
            'to2_id'  => $comment->to2_id === $loginId,
            'to3_id'  => $comment->to3_id === $loginId,
            default   => false,
        };

        if (! $isValid) {
            return redirect()->route('task.show', $task->id)->with('error', '既読権限がありません。');
        }

        // 対応する既読カラムを更新
        match ($recipient) {
            'to_id'   => $comment->already_read  = 1,
            'to2_id'  => $comment->already_read2 = 1,
            'to3_id'  => $comment->already_read3 = 1,
        };

        $comment->save();

        if ($request->filled('redirect_url')) {
        return redirect($request->input('redirect_url'))->with('success', 'コメントを既読にしました。');
        }

        return redirect()->route('task.show', $task->id)->with('success', 'コメントを既読にしました。');
    }

}
