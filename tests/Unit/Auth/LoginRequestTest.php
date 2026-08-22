<?php

use App\Http\Requests\Auth\LoginRequest;

test('login request requires an email and password', function () {
    // 1. 로그인 요청 객체를 생성한다
    // 2. 요청 객체가 선언한 검증 규칙을 확인한다
    // 3. 이메일과 비밀번호 필드가 필수인지 검증한다
    $rules = (new LoginRequest())->rules();

    expect($rules)->toHaveKeys(['email', 'password']);
    expect($rules['email'])->toContain('required');
    expect($rules['password'])->toContain('required');
});
