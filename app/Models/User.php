<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// MustVerifyEmail interface는 Laravel이 이 User의 인증 상태를 자동 메일 발송과 verified 접근 제한에 사용하게 한다
class User extends Authenticatable implements MustVerifyEmail
{
    // $fillable은 User::create() 같은 대량 할당에서 저장을 허용할 필드를 지정한다
    // 요청값 전체가 임의의 컬럼을 덮어쓰지 못하도록 허용 목록을 명시한다
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    // $hidden은 모델을 배열이나 JSON으로 변환할 때 외부에 노출하지 않을 필드를 지정한다
    // 비밀번호와 세션 토큰이 응답에 포함되는 것을 방지한다
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // HasFactory Trait은 테스트와 시더에서 UserFactory를 이용해 테스트용 사용자를 생성하게 한다
    use HasFactory, Notifiable;

    // casts()는 데이터베이스 값을 PHP에서 사용할 때 원하는 타입으로 자동 변환한다
    // 이메일 인증 시각은 날짜 객체로, 비밀번호는 저장 시 해시 처리되도록 지정한다
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1. seller_id가 현재 User id인 Store 한 건을 찾는다
    // 2. stores.seller_id가 unique이므로 한 User와 한 Store의 HasOne 관계를 반환한다
    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'seller_id');
    }

    // 1. 현재 User의 Store를 거쳐 그 Store가 보유한 Product들을 찾는다
    // 2. users → stores → products의 두 단계를 한 관계로 조회하기 위해 HasManyThrough를 사용한다
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Store::class, 'seller_id', 'store_id', 'id', 'id');
    }
}
