<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InsuranceCompanyController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExportController;

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
    Route::delete('/client/{client}', [ClientController::class, 'destroy'])->name('client.destroy');


    //inquiry
    Route::get('/inquiry', function () {return view('inquiry.index');})->name('inquiry.index');

    //consultation
    Route::get('/consultation', function () {return view('consultation.index');})->name('consultation.index');

    //business
    Route::get('/business', function () {return view('business.index');})->name('business.index');

    //opponent
    Route::get('/opponent', function () {return view('opponent.index');})->name('opponent.index');

    //advisory
    Route::get('/advisory', function () {return view('advisory.index');})->name('advisory.index');

    //advisory_consultation
    Route::get('/advisory_consultation', function () {return view('advisory_consultation.index');})->name('advisory_consultation.index');

    //task
    Route::get('/task', function () {return view('task.index');})->name('task.index');

    //negotiation
    Route::get('/negotiation', function () {return view('negotiation.index');})->name('negotiation.index');

    //accountancy
    Route::get('/accountancy', function () {return view('accountancy.index');})->name('accountancy.index');

    //court
    Route::get('/court', [CourtController::class, 'index'])->name('court.index');
    Route::get('/court/create', [CourtController::class, 'create'])->name('court.create');
    Route::post('/court', [CourtController::class, 'store'])->name('court.store');
    Route::get('/court/{court}', [CourtController::class, 'show'])->name('court.show');
    Route::put('/court/{court}', [CourtController::class, 'update'])->name('court.update');
    Route::delete('/court/{court}', [CourtController::class, 'destroy'])->name('court.destroy');

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

// 認証関連のルート（Breeze が自動生成）
require __DIR__.'/auth.php';