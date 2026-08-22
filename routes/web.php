<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 1. 별도 파일에 작성한 인증 라우트 목록을 불러온다
// 2. Laravel이 애플리케이션의 웹 라우트를 등록할 때 인증 라우트도 함께 등록한다
// 3. 회원가입, 로그인, 로그아웃 URL을 실제 애플리케이션에서 사용할 수 있게 한다
require __DIR__.'/auth.php';
