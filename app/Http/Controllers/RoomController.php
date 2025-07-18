<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Room;
use Illuminate\Validation\Rule;

class RoomController extends Controller
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

    // 施設一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('room_name')) {
            $query->where('room_name', 'like', '%' . $request->room_name . '%');
        }
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        $rooms = $query->paginate(15);
        return view('room.index', compact('rooms'));
    }

        // 施設追加画面
        public function create()
        {
            return view('room.create');
        }
    
        // 施設追加処理
        public function store(Request $request)
        {        
            $request->validate([
                'room_name' => ['required','string','max:255', Rule::unique('rooms')],
                'calendar_id' => ['required','string','max:255', Rule::unique('rooms')],
                'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
                'importantnotes' => 'nullable|string|max:255',
            ]);
        
            Room::create([
                'room_name' => $request->room_name,
                'calendar_id' => $request->calendar_id,
                'office_id' => $request->office_id,
                'importantnotes' => $request->importantnotes,
            ]);
        
            return redirect()->route('room.index')->with('success', '施設を追加しました！');
        }
        
        // 施設詳細表示
        public function show(Room $room)
        {
            return view('room.show', compact('room'));
        }
        
        public function update(Request $request, Room $room)
        {
            $request->validate([
                'room_name' => ['required','string','max:255',Rule::unique('rooms')->ignore($room->id),],
                'calendar_id' => ['required','string','max:255',Rule::unique('rooms')->ignore($room->id),],
                'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
                'importantnotes' => 'nullable|string|max:255',
            ]);
    
            $room->update([
                'room_name' => $request->room_name,
                'calendar_id' => $request->calendar_id,
                'office_id' => $request->office_id,
                'importantnotes' => $request->importantnotes,
            ]);
    
            return redirect()->route('room.show', $room->id)->with('success', '施設を更新しました！');
        }
        // 施設削除処理
        public function destroy(Room $room)
        {
        try {
            $room->delete();
            return redirect()->route('room.index')->with('success', '施設を削除しました');
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
