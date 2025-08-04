<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
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

    /**
     * 自分自身かどうかチェック（管理者以外は他人NG）
     
    *private function ensureIsSelfOrAdmin($userId)
    *{
    *    $loginUser = auth()->user();
    *    if ($loginUser->role_type != 1 && $loginUser->id !== $userId) {
    *        abort(403, 'この操作は許可されていません。');
    *    }
    *}
    */
    
    // ユーザ一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }
        if ($request->filled('role_type')) {
            $query->where('role_type', $request->role_type);
        }

        $users = $query->paginate(15);
        return view('users.index', compact('users'));
    }

    // ユーザ追加画面
    public function create()
    {
        $this->ensureIsAdmin();
        return view('users.create');
    }

    // ユーザ追加処理
    public function store(Request $request)
    {
        $this->ensureIsAdmin();
    
        $request->validate([
            'user_id' => ['required','string','min:8','unique:users',],
            'name' => 'required|string|max:255',
            'employee_type' => 'required|in:' . implode(',', array_keys(config('master.employee_types'))),
            'office_id'     => 'nullable|in:' . implode(',', array_keys(config('master.offices'))),
            'role_type'     => 'required|in:' . implode(',', array_keys(config('master.role_types'))),
            'user_status'   => 'required|in:' . implode(',', array_keys(config('master.user_statuses'))),
            'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
            'email'         => 'nullable|email|max:255',
            'email2'        => 'nullable|email|max:255',
            'slack_channel_id'  => 'nullable|string|max:255',
            'password'          => ['required','confirmed','password_strength'],
        ]);
    
        User::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'employee_type' => $request->employee_type,
            'office_id' => $request->office_id,
            'role_type' => $request->role_type,
            'phone_number' => $request->phone_number,
            'phone_number2' => $request->phone_number2,
            'email' => $request->email,
            'email2' => $request->email2,
            'slack_channel_id' => $request->slack_channel_id,
            'password' => Hash::make($request->password),
        ]);
    
        return redirect()->route('users.index')->with('success', 'ユーザを追加しました！');
    }

    // ユーザ詳細表示
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureIsAdmin();
    
        $request->validate([
            'user_id' => ['required','string','min:8',Rule::unique('users', 'user_id')->ignore($user->id),],
            'name' => 'required|string|max:255',
            'employee_type' => 'required|in:' . implode(',', array_keys(config('master.employee_types'))),
            'office_id'     => 'nullable|in:' . implode(',', array_keys(config('master.offices'))),
            'role_type'     => 'required|in:' . implode(',', array_keys(config('master.role_types'))),
            'user_status'   => 'required|in:' . implode(',', array_keys(config('master.user_statuses'))),
            'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
            'email'         => 'nullable|email|max:255',
            'email2'        => 'nullable|email|max:255',
            'slack_channel_id'  => 'nullable|string|max:255',
        ]);
    
        $user->update([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'employee_type' => $request->employee_type,
            'office_id' => $request->office_id,
            'role_type' => $request->role_type,
            'user_status' => $request->user_status,
            'phone_number' => $request->phone_number,
            'phone_number2' => $request->phone_number2,
            'email' => $request->email,
            'email2' => $request->email2,
            'slack_channel_id' => $request->slack_channel_id,
        ]);
    
        return redirect()->route('users.show', $user->id)->with('success', 'ユーザ情報を更新しました！');
    }

    // ユーザ削除
    public function destroy(User $user)
    {
        $this->ensureIsAdmin();
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'ユーザを削除しました');
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

    // 自分のパスワード変更画面
    public function editPassword()
    {
        $loginUser = auth()->user();
        return view('users.password');
    }

    // パスワード変更処理
    public function updatePassword(Request $request)
    {
        $loginUser = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', 'password_strength'],
        ]);

        $loginUser->password = Hash::make($request->new_password);
        $loginUser->save();

        return redirect()->route('users.show', $loginUser->id)
                         ->with('success', 'パスワードを変更しました');
    }

    // パスワード初期化処理
    public function resetPassword(Request $request, User $user)
    {
        $this->ensureIsAdmin();

        $newPassword = $this->generateStrongPassword(12);
        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->route('users.show', $user->id)
                         ->with('success', 'パスワードを初期化しました。新しいパスワード：' . $newPassword);
    }

    private function generateStrongPassword($length = 12): string
    {
    // 各文字種
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    // 最低1文字ずつ確保
    $password = [
        $upper[random_int(0, strlen($upper) - 1)],
        $lower[random_int(0, strlen($lower) - 1)],
        $digits[random_int(0, strlen($digits) - 1)],
        $symbols[random_int(0, strlen($symbols) - 1)],
    ];

    // 残りの文字をランダムに組み合わせ
    $all = $upper . $lower . $digits . $symbols;
    for ($i = 4; $i < $length; $i++) {
        $password[] = $all[random_int(0, strlen($all) - 1)];
    }

    // シャッフルして順番ランダム化
    shuffle($password);

    return implode('', $password);
    }

    // ユーザ検索API
    public function search(Request $request)
    {
        $keyword = $request->input('q');
    
        $results = [];
    
        if ($keyword) {
            $results = User::where('name', 'like', "%{$keyword}%")
                ->select('id', 'name')
                ->limit(10)
                ->get()
                ->map(fn($user) => ['id' => $user->id, 'text' => $user->name]);
        }
    
        return response()->json(['results' => $results]);
    }

}
