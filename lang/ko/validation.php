<?php

// Laravel이 검증 규칙별 에러 메시지를 한국어로 출력하도록 사용하는 언어 파일이다
return [
    // 기본 입력값 검증
    'required' => ':attribute 필드는 필수입니다.',
    'string' => ':attribute은(는) 문자열이어야 합니다.',

    // 입력 형식과 대소문자 검증
    'email' => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'lowercase' => ':attribute은(는) 소문자여야 합니다.',

    // 입력값 길이 검증
    'max' => [
        'array' => ':attribute에는 최대 :max개의 항목만 입력할 수 있습니다.',
        'file' => ':attribute은(는) :max킬로바이트를 초과할 수 없습니다.',
        'numeric' => ':attribute은(는) :max보다 클 수 없습니다.',
        'string' => ':attribute은(는) 최대 :max자까지 입력할 수 있습니다.',
    ],
    'min' => [
        'array' => ':attribute에는 최소 :min개의 항목이 필요합니다.',
        'file' => ':attribute은(는) 최소 :min킬로바이트여야 합니다.',
        'numeric' => ':attribute은(는) 최소 :min이어야 합니다.',
        'string' => ':attribute은(는) 최소 :min자 이상이어야 합니다.',
    ],

    // 필드 간 관계와 선택값 검증
    'confirmed' => ':attribute 확인이 일치하지 않습니다.',
    'same' => ':attribute은(는) :other과(와) 일치해야 합니다.',
    'in' => '선택한 :attribute 값이 올바르지 않습니다.',
    'unique' => '이미 사용 중인 :attribute입니다.',
    'current_password' => '비밀번호가 올바르지 않습니다.',

    // Rules\Password가 사용하는 비밀번호 세부 검증
    'password' => [
        'letters' => ':attribute에는 최소 하나의 문자가 포함되어야 합니다.',
        'mixed' => ':attribute에는 대문자와 소문자가 각각 하나 이상 포함되어야 합니다.',
        'numbers' => ':attribute에는 최소 하나의 숫자가 포함되어야 합니다.',
        'symbols' => ':attribute에는 최소 하나의 기호가 포함되어야 합니다.',
        'uncompromised' => '입력한 :attribute이(가) 데이터 유출에 포함된 적이 있습니다. 다른 값을 사용하세요.',
    ],

    // 오류 메시지에 표시할 사람이 읽기 쉬운 필드명
    'attributes' => [
        'name' => '이름',
        'email' => '이메일',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'user_type' => '계정 유형',
    ],
];
