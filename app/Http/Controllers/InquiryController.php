<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\Consultation;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
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

    // 問い合わせ一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Inquiry::query();

        if ($request->filled('inquiries_name_kanji')) {
            $query->where('inquiries_name_kanji', 'like', '%' . $request->inquiries_name_kanji . '%');
        }
        if ($request->filled('inquiries_name_kana')) {
            $query->where('inquiries_name_kana', 'like', '%' . $request->inquiries_name_kana . '%');
        }
        if ($request->filled('receptiondate')) {
            $query->whereDate('receptiondate', $request->receptiondate);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $inquiries = $query->paginate(15);
        return view('inquiry.index', compact('inquiries'));
    }

    // 問い合わせ追加画面
    public function create()
    {
        return view('inquiry.create');
    }

    // 問い合わせ追加処理
    public function store(Request $request)
    {        
        $request->validate([
            'receptiondate' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(config('master.inquiry_status'))),
            'inquiries_name_kanji' => 'required|string|max:255',
            'inquiries_name_kana' => 'required|string|max:255',
            'last_name_kanji' => 'required|string|max:100',
            'first_name_kanji' => 'required|string|max:155',
            'last_name_kana' => 'required|string|max:100',
            'first_name_kana' => 'required|string|max:155',
            'corporate_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'state' => 'nullable|string|max:100',
            'firstchoice_date' => 'nullable|date',
            'firstchoice_time' => 'nullable|date_format:H:i',
            'secondchoice_date' => 'nullable|date',
            'secondchoice_time' => 'nullable|date_format:H:i',
            'inquirycontent' => 'nullable|string|max:1000',
            'route' => 'nullable|in:' . implode(',', array_keys(config('master.routes'))),
            'routedetail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'averageovertimehoursperweek' => 'nullable|string|max:10',
            'monthlyincome' => 'nullable|string|max:10',
            'lengthofservice' => 'nullable|string|max:10',
            ],
        );

        $firstChoice = null;
        if ($request->filled('firstchoice_date') && $request->filled('firstchoice_time')) {
            $firstChoice = \Carbon\Carbon::parse($request->firstchoice_date . ' ' . $request->firstchoice_time);
        }

        $secondChoice = null;
        if ($request->filled('secondchoice_date') && $request->filled('secondchoice_time')) {
            $secondChoice = \Carbon\Carbon::parse($request->secondchoice_date . ' ' . $request->secondchoice_time);
        }

        Inquiry::create([
            'receptiondate' => $request->receptiondate,
            'status' => $request->status,
            'inquiries_name_kanji' => $request->inquiries_name_kanji,
            'inquiries_name_kana' => $request->inquiries_name_kana,
            'last_name_kanji' => $request->last_name_kanji,
            'first_name_kanji' => $request->first_name_kanji,
            'last_name_kana' => $request->last_name_kana,
            'first_name_kana' => $request->first_name_kana,
            'corporate_name' => $request->corporate_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'state' => $request->state,
            'firstchoice_datetime' => $firstChoice,
            'secondchoice_datetime' => $secondChoice,
            'inquirycontent' => $request->inquirycontent,
            'route' => $request->route,
            'routedetail' => $request->routedetail,
            'averageovertimehoursperweek' => $request->averageovertimehoursperweek,
            'monthlyincome' => $request->monthlyincome,
            'lengthofservice' => $request->lengthofservice,
        ]);

        return redirect()->route('inquiry.index')->with('success', '問合せを追加しました！');
    }

    // 問い合わせ詳細
    public function show(Inquiry $inquiry)
    {

        $inquiry->load([
            'consultation',
        ]);


        return view('inquiry.show', compact('inquiry'));
    }
    // 問い合わせ編集処理
    public function update(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'receptiondate' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(config('master.inquiry_status'))),
            'inquiries_name_kanji' => 'required|string|max:255',
            'inquiries_name_kana' => 'required|string|max:255',
            'last_name_kanji' => 'required|string|max:100',
            'first_name_kanji' => 'required|string|max:155',
            'last_name_kana' => 'required|string|max:100',
            'first_name_kana' => 'required|string|max:155',
            'corporate_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'state' => 'nullable|string|max:100',
            'firstchoice_date' => 'nullable|date',
            'firstchoice_time' => 'nullable|date_format:H:i',
            'secondchoice_date' => 'nullable|date',
            'secondchoice_time' => 'nullable|date_format:H:i',
            'inquirycontent' => 'nullable|string|max:1000',
            'route' => 'nullable|in:' . implode(',', array_keys(config('master.routes'))),
            'routedetail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'averageovertimehoursperweek' => 'nullable|string|max:10',
            'monthlyincome' => 'nullable|string|max:10',
            'lengthofservice' => 'nullable|string|max:10',
            // 一時的にexistsをコメントアウト
            // 'consultation_id' => 'nullable|exists:consultations,id',
            'consultation_id' => 'nullable|integer',
            ],
        );

        $firstChoice = null;
        if ($request->filled('firstchoice_date') && $request->filled('firstchoice_time')) {
            $firstChoice = \Carbon\Carbon::parse($request->firstchoice_date . ' ' . $request->firstchoice_time);
        }

        $secondChoice = null;
        if ($request->filled('secondchoice_date') && $request->filled('secondchoice_time')) {
            $secondChoice = \Carbon\Carbon::parse($request->secondchoice_date . ' ' . $request->secondchoice_time);
        }

        $inquiry->update([
            'receptiondate' => $request->receptiondate,
            'status' => $request->status,
            'inquiries_name_kanji' => $request->inquiries_name_kanji,
            'inquiries_name_kana' => $request->inquiries_name_kana,
            'last_name_kanji' => $request->last_name_kanji,
            'first_name_kanji' => $request->first_name_kanji,
            'last_name_kana' => $request->last_name_kana,
            'first_name_kana' => $request->first_name_kana,
            'corporate_name' => $request->corporate_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'state' => $request->state,
            'firstchoice_datetime' => $firstChoice,
            'secondchoice_datetime' => $secondChoice,
            'inquirycontent' => $request->inquirycontent,
            'route' => $request->route,
            'routedetail' => $request->routedetail,
            'averageovertimehoursperweek' => $request->averageovertimehoursperweek,
            'monthlyincome' => $request->monthlyincome,
            'lengthofservice' => $request->lengthofservice,
            'consultation_id' => $request->consultation_id,

        ]);

        return redirect()->route('inquiry.show', $inquiry->id)->with('success', '問合せを更新しました！');
    }

    // 問い合わせ削除処理
    public function destroy(Inquiry $inquiry)
    {
        $this->ensureIsAdmin();
        $inquiry->delete();
        return redirect()->route('inquiry.index')->with('success', '問合せを削除しました！');
    }

}
