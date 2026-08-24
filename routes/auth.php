<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// 1. 아직 로그인하지 않은 사용자만 회원가입, 로그인, 비밀번호 재설정 화면에 접근한다
// 2. guest 미들웨어가 이미 인증된 사용자의 접근을 차단한다
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// 1. 로그인한 사용자만 이메일 인증, 비밀번호 확인, 로그아웃 기능을 사용할 수 있다
// 2. auth 미들웨어가 현재 로그인한 사용자를 확인한다
// 3. 이메일 인증 링크는 signed URL과 요청 횟수 제한으로 추가 보호한다
Route::middleware('auth')->group(function () {
    // Invokable Controller는 __invoke() 메서드 하나를 실행하는 Controller 연결 방식이다
    // 이메일 인증 안내 화면을 단일 동작 Controller에 연결한다
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // 'signed'는 Laravel이 만든 URL 서명이 현재 URL과 일치하고 만료되지 않았는지 확인해 변조된 인증 링크를 차단한다
    // 'throttle:6,1'은 같은 요청자가 1분에 6회만 접근하게 해 인증 링크 엔드포인트의 반복 요청을 제한한다
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1',])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
