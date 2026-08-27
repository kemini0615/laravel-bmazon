<?php

use App\Http\Controllers\User\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\Seller\DashboardController as SellerDashboardController;
use Illuminate\Support\Facades\Route;

// 1. 브라우저의 GET / 요청을 User HomeController의 index 메서드에 전달한다
// 2. home.index 이름을 부여해 Blade와 Controller에서 URL 문자열 대신 이름으로 홈 링크를 생성하게 한다
// Route Facade는 URL과 Controller 동작을 연결하는 Laravel 라우팅 기능이다
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// 1. auth 미들웨어로 로그인한 사용자만 대시보드에 접근하게 한다
// 2. verified 미들웨어로 email_verified_at이 있는 사용자만 대시보드에 접근하게 한다
// 3. 미인증 사용자는 verification.notice 라우트로 이동해 이메일 인증을 완료하게 한다
// 4. 각 Controller가 user_type을 확인해 Buyer와 Seller 대시보드를 구분한다
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])
        ->name('buyer.dashboard');

    Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])
        ->name('seller.dashboard');
});

// 1. 별도 파일에 작성한 인증 라우트 목록을 불러온다
// 2. Laravel이 애플리케이션의 웹 라우트를 등록할 때 인증 라우트도 함께 등록한다
// 3. 회원가입, 로그인, 로그아웃 URL을 실제 애플리케이션에서 사용할 수 있게 한다
require __DIR__.'/auth.php';
