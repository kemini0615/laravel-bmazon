<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'position',
        'is_active',
        'image',
        'icon',
        'is_featured',
    ];

    // 1. 현재 카테고리의 parent_id로 부모 Category 한 건을 찾는다
    // 2. 최상위 카테고리는 parent_id가 null이므로 부모 관계도 null이다
    public function parent(): BelongsTo
    {
        // belongsTo()는 현재 모델이 외래 키를 갖고 상대 모델 하나를 가리키는 Eloquent 관계다
        return $this->belongsTo(self::class, 'parent_id');
    }

    // 1. 다른 카테고리 중 parent_id가 현재 카테고리 id인 행들을 찾는다
    // 2. 현재 카테고리의 바로 아래 자식 목록을 반환한다
    public function children(): HasMany
    {
        // hasMany()는 상대 모델이 현재 모델의 id를 외래 키로 여러 건 참조하는 Eloquent 관계다
        return $this->hasMany(self::class, 'parent_id');
    }

    // 1. 바로 아래 자식 카테고리의 id를 수집한다
    // 2. 각 자식의 하위 카테고리도 재귀적으로 수집한다
    // 3. 현재 카테고리를 제외한 모든 하위 카테고리 id 배열을 반환한다
    public function allChildrenIds(): array
    {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->allChildrenIds());
        }

        return $ids;
    }

    // 1. 중간 테이블 category_product를 통해 현재 카테고리에 연결된 상품을 조회한다
    // 2. 상품 하나가 여러 카테고리에 속할 수 있으므로 다대다 관계를 사용한다
    public function products(): BelongsToMany
    {
        // belongsToMany()는 두 모델이 중간 테이블의 category_id, product_id로 연결되는 Eloquent 관계다
        return $this->belongsToMany(Product::class);
    }
}
