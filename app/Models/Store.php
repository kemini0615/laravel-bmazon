<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'id',
        'seller_id',
        'logo',
        'banner',
        'name',
        'email',
        'phone',
        'address',
        'short_description',
        'long_description',
    ];

    // 1. 현재 Store의 seller_id로 이 상점을 소유한 User 한 건을 찾는다
    // 2. Store가 seller_id 외래 키를 가지므로 BelongsTo 관계를 반환한다
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // 1. store_id가 현재 Store id인 Product들을 찾는다
    // 2. 상점 하나에 여러 상품이 속하므로 HasMany 관계를 반환한다
    public function products(): HasMany
    {
        // hasMany()는 상대 테이블이 현재 모델의 id를 외래 키로 여러 건 저장한 관계다
        return $this->hasMany(Product::class);
    }
}
