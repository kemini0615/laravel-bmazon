<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    // $guarded가 빈 배열이면 Eloquent 대량 할당에서 모든 컬럼을 허용하며, 검증된 플래시세일 설정을 한 번에 저장하기 위해 사용한다
    protected $guarded = [];

    // $casts는 JSON으로 저장된 products 값을 PHP 배열로 자동 변환해 Product id 목록 조회에 사용하게 한다
    protected $casts = [
        'products' => 'array',
    ];
}
