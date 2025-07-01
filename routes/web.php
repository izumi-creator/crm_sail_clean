<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InsuranceCompanyController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\RelatedPartyController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CourtTaskController;
use App\Http\Controllers\AdvisoryContractController;
use App\Http\Controllers\AdvisoryConsultationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NegotiationController;


Route::get('/', function () {
    return redirect()->route('dashboard'); // ログイン後はダッシュボードへ
});

// 認証が必要なルート
Route::middleware(['auth', 'verified'])->group(function () {
    // dashboard
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

    // client
    Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');
    Route::post('/client', [ClientController::class, 'store'])->name('client.store');
    Route::get('/client/{client}', [ClientController::class, 'show'])->name('client.show');
    Route::put('/client/{client}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/client/{client}', [ClientController::class, 'destroy'])->name('client.destroy');

    Route::get('/api/client/search', [ClientController::class, 'search'])->name('client.search');

    //inquiry
    Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');
    Route::get('/inquiry/create', [InquiryController::class, 'create'])->name('inquiry.create');
    Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
    Route::get('/inquiry/{inquiry}', [InquiryController::class, 'show'])->name('inquiry.show');
    Route::put('/inquiry/{inquiry}', [InquiryController::class, 'update'])->name('inquiry.update');
    Route::delete('/inquiry/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiry.destroy');

    //consultation
    Route::get('/consultation', [ConsultationController::class, 'index'])->name('consultation.index');
    Route::get('/consultation/create', [ConsultationController::class, 'create'])->name('consultation.create');
    Route::post('/consultation', [ConsultationController::class, 'store'])->name('consultation.store');
    Route::get('/consultation/{consultation}', [ConsultationController::class, 'show'])->name('consultation.show');
    Route::put('/consultation/{consultation}', [ConsultationController::class, 'update'])->name('consultation.update');
    Route::delete('/consultation/{consultation}', [ConsultationController::class, 'destroy'])->name('consultation.destroy');

    Route::get('/api/consultations/search', [ConsultationController::class, 'search'])->name('consultations.search');

    Route::post('/consultation/{consultation}/conflict-check', [ConsultationController::class, 'conflictUpdate'])
    ->name('consultation.conflict.update');

    //business
    Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
    Route::get('/business/create', [BusinessController::class, 'create'])->name('business.create');
    Route::post('/business', [BusinessController::class, 'store'])->name('business.store');
    Route::get('/business/{business}', [BusinessController::class, 'show'])->name('business.show');
    Route::put('/business/{business}', [BusinessController::class, 'update'])->name('business.update');
    Route::delete('/business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');

    Route::get('/api/businesses/search', [BusinessController::class, 'search'])->name('businesses.search');


    // CourtTask（裁判所対応）ルート群
    // 受任案件に紐づく裁判所対応の作成・登録
    Route::prefix('/business/{business}/court-task')->name('court_task.')->group(function () {
        Route::get('/create', [CourtTaskController::class, 'create'])->name('create');
        Route::post('/', [CourtTaskController::class, 'store'])->name('store');
    });

    // 編集・表示・削除など（business_idは不要）
    Route::get('/court-task/{court_task}', [CourtTaskController::class, 'show'])->name('court_task.show');
    Route::put('/court-task/{court_task}', [CourtTaskController::class, 'update'])->name('court_task.update');
    Route::delete('/court-task/{court_task}', [CourtTaskController::class, 'destroy'])->name('court_task.destroy');

    //relatedparty
    Route::get('/relatedparty', [RelatedPartyController::class, 'index'])->name('relatedparty.index');
    Route::get('/relatedparty/create', [RelatedPartyController::class, 'create'])->name('relatedparty.create');
    Route::post('/relatedparty', [RelatedPartyController::class, 'store'])->name('relatedparty.store');
    Route::get('/relatedparty/{relatedparty}', [RelatedPartyController::class, 'show'])->name('relatedparty.show');
    Route::put('/relatedparty/{relatedparty}', [RelatedPartyController::class, 'update'])->name('relatedparty.update');
    Route::delete('/relatedparty/{relatedparty}', [RelatedPartyController::class, 'destroy'])->name('relatedparty.destroy');

    //advisory
    Route::get('/advisory', [AdvisoryContractController::class, 'index'])->name('advisory.index');
    Route::get('/advisory/create', [AdvisoryContractController::class, 'create'])->name('advisory.create');
    Route::post('/advisory', [AdvisoryContractController::class, 'store'])->name('advisory.store');
    Route::get('/advisory/{advisory}', [AdvisoryContractController::class, 'show'])->name('advisory.show');
    Route::put('/advisory/{advisory}', [AdvisoryContractController::class, 'update'])->name('advisory.update');
    Route::delete('/advisory/{advisory}', [AdvisoryContractController::class, 'destroy'])->name('advisory.destroy');

    Route::get('/api/advisories/search', [AdvisoryContractController::class, 'search'])->name('advisory.search');

    Route::post('/advisory/{advisory}/conflict-check', [AdvisoryContractController::class, 'conflictUpdate'])
    ->name('advisory.conflict.update');

    //advisory_consultation
    Route::get('/advisory-consultation', [AdvisoryConsultationController::class, 'index'])->name('advisory_consultation.index');
    Route::get('/advisory-consultation/create', [AdvisoryConsultationController::class, 'create'])->name('advisory_consultation.create');
    Route::post('/advisory-consultation', [AdvisoryConsultationController::class, 'store'])->name('advisory_consultation.store');
    Route::get('/advisory-consultation/{advisory_consultation}', [AdvisoryConsultationController::class, 'show'])->name('advisory_consultation.show');
    Route::put('/advisory-consultation/{advisory_consultation}', [AdvisoryConsultationController::class, 'update'])->name('advisory_consultation.update');
    Route::delete('/advisory-consultation/{advisory_consultation}', [AdvisoryConsultationController::class, 'destroy'])->name('advisory_consultation.destroy');
    
    Route::get('/api/advisory_consultations/search', [AdvisoryConsultationController::class, 'search'])->name('advisory_consultation.search');

    Route::post('/advisory-consultation/{advisory_consultation}/conflict-check', [AdvisoryConsultationController::class, 'conflictUpdate'])
    ->name('advisory_consultation.conflict.update');

    //task
    Route::get('/task', [TaskController::class, 'index'])->name('task.index');
    Route::get('/task/create', [TaskController::class, 'create'])->name('task.create');
    Route::post('/task', [TaskController::class, 'store'])->name('task.store');
    Route::get('/task/{task}', [TaskController::class, 'show'])->name('task.show');
    Route::put('/task/{task}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{task}', [TaskController::class, 'destroy'])->name('task.destroy');

    //negotiation
    Route::get('/negotiation', [NegotiationController::class, 'index'])->name('negotiation.index');
    Route::get('/negotiation/create', [NegotiationController::class, 'create'])->name('negotiation.create');
    Route::post('/negotiation', [NegotiationController::class, 'store'])->name('negotiation.store');
    Route::get('/negotiation/{negotiation}', [NegotiationController::class, 'show'])->name('negotiation.show');
    Route::put('/negotiation/{negotiation}', [NegotiationController::class, 'update'])->name('negotiation.update');
    Route::delete('/negotiation/{negotiation}', [NegotiationController::class, 'destroy'])->name('negotiation.destroy');

    //accountancy
    Route::get('/accountancy', function () {return view('accountancy.index');})->name('accountancy.index');

    //court
    Route::get('/court', [CourtController::class, 'index'])->name('court.index');
    Route::get('/court/create', [CourtController::class, 'create'])->name('court.create');
    Route::post('/court', [CourtController::class, 'store'])->name('court.store');
    Route::get('/court/{court}', [CourtController::class, 'show'])->name('court.show');
    Route::put('/court/{court}', [CourtController::class, 'update'])->name('court.update');
    Route::delete('/court/{court}', [CourtController::class, 'destroy'])->name('court.destroy');

    Route::get('/api/court/search', [CourtController::class, 'search'])->name('courts.search');

    //insurance
    Route::get('/insurance', [InsuranceCompanyController::class, 'index'])->name('insurance.index');
    Route::get('/insurance/create', [InsuranceCompanyController::class, 'create'])->name('insurance.create');
    Route::post('/insurance', [InsuranceCompanyController::class, 'store'])->name('insurance.store');
    Route::get('/insurance/{insurance}', [InsuranceCompanyController::class, 'show'])->name('insurance.show');
    Route::put('/insurance/{insurance}', [InsuranceCompanyController::class, 'update'])->name('insurance.update');
    Route::delete('/insurance/{insurance}', [InsuranceCompanyController::class, 'destroy'])->name('insurance.destroy');

    //users
    Route::get('/user/password', [UserController::class, 'editPassword'])->name('users.editPassword');
    Route::put('/user/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

    Route::get('/api/users/search', [UserController::class, 'search'])->name('users.search');
    
    //room
    Route::get('/room', [RoomController::class, 'index'])->name('room.index');
    Route::get('/room/create', [RoomController::class, 'create'])->name('room.create');
    Route::post('/room', [RoomController::class, 'store'])->name('room.store');
    Route::get('/room/{room}', [RoomController::class, 'show'])->name('room.show');
    Route::put('/room/{room}', [RoomController::class, 'update'])->name('room.update');
    Route::delete('/room/{room}', [RoomController::class, 'destroy'])->name('room.destroy');
    
    //export（データダウンロード）
    Route::get('/export/download', function () {return view('export.index');})->name('export.index');
    Route::post('/export/download', [ExportController::class, 'download'])->name('export.download');

});

// 認証関連のルート
require __DIR__.'/auth.php';