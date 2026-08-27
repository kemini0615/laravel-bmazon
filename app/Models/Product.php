<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // SoftDeletes trait은 delete() 시 행을 즉시 삭제하지 않고 deleted_at에 삭제 시각을 저장하게 한다
    use SoftDeletes;

    // 1. category_product 중간 테이블에서 현재 Product id와 연결된 Category들을 찾는다
    // 2. 상품 하나가 여러 카테고리에 속할 수 있으므로 다대다 관계를 반환한다
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // 1. 현재 상품에 연결된 ProductImage 중 order가 가장 앞선 한 건을 조회한다
    // 2. 상품 카드의 대표 이미지를 일관된 순서로 표시하기 위해 HasOne 관계에 정렬을 추가한다
    public function primaryImage(): HasOne
    {
        // hasOne()은 상대 테이블이 현재 모델의 id를 외래 키로 참조하는 한 건을 조회하는 Eloquent 관계다
        return $this->hasOne(ProductImage::class)->orderBy('order');
    }

    // 1. 현재 상품에 연결된 ProductImage 전체를 조회한다
    // 2. 관리자가 지정한 order 순서대로 상품 갤러리를 표시한다
    public function images(): HasMany
    {
        // hasMany()는 상대 테이블이 현재 모델의 id를 외래 키로 참조하는 여러 건을 조회하는 Eloquent 관계다
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    // 1. 현재 Product의 store_id로 이 상품을 판매하는 Store 한 건을 찾는다
    // 2. 상품 하나는 하나의 Store에 속하므로 BelongsTo 관계를 반환한다
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // 1. 현재 Product의 brand_id로 이 상품의 Brand 한 건을 찾는다
    // 2. 상품 하나는 하나의 Brand에 속하므로 BelongsTo 관계를 반환한다
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // 1. 현재 상품의 product_id와 연결된 ProductReview 목록을 조회한다
    // 2. 홈 화면에서 리뷰 평균과 평점순 상품을 계산하기 위해 HasMany 관계를 반환한다
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    // 1. 현재 상품에 리뷰가 없으면 평점 0을 반환한다
    // 2. 리뷰가 있으면 rating 평균을 계산해 소수점 한 자리로 반올림한다
    public function rating(): float
    {
        if (! $this->reviews()->exists()) {
            return 0;
        }

        return round($this->reviews()->avg('rating'), 1);
    }
}
