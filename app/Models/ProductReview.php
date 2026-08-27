<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    // 1. 현재 리뷰의 user_id로 작성자 User 한 건을 조회한다
    // 2. 리뷰가 사용자를 가리키는 외래 키를 가지므로 BelongsTo 관계를 반환한다
    public function user(): BelongsTo
    {
        // belongsTo()는 현재 모델의 외래 키로 부모 모델 한 건을 조회하는 Eloquent 관계다
        return $this->belongsTo(User::class);
    }

    // 1. 현재 리뷰의 product_id로 대상 Product 한 건을 조회한다
    // 2. 리뷰가 상품을 가리키는 외래 키를 가지므로 BelongsTo 관계를 반환한다
    public function product(): BelongsTo
    {
        // belongsTo()는 현재 모델의 외래 키로 부모 모델 한 건을 조회하는 Eloquent 관계다
        return $this->belongsTo(Product::class);
    }
}
