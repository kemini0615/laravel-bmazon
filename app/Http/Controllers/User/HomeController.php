<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\HeroBanner;
use App\Models\PopularCategory;
use App\Models\Product;
use App\Models\ProductSection;
use App\Models\Slider;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    // 1. 추천 카테고리와 홈 배너 설정을 조회한다
    // 2. 인기 카테고리·플래시세일·상품 섹션에 연결된 상품을 조회한다
    // 3. 인기·신규·추천·평점순 상품을 조회한다
    // 4. 조회 결과를 User 홈 Blade 뷰에 전달한다
    public function index(): View
    {
        // withCount()는 products 관계의 개수를 products_count 컬럼으로 함께 조회해 카테고리별 상품 수를 추가 쿼리 없이 표시하게 한다
        // whereIsFeatured()는 is_featured 컬럼을 조건으로 사용하는 Eloquent 동적 where이며 추천 카테고리만 고르기 위해 사용한다
        // take(15)는 SQL의 LIMIT 15처럼 데이터베이스가 반환할 카테고리를 최대 15개로 제한한다
        $featuredCategories = Category::withCount('products')
            ->whereIsFeatured(true)
            ->take(15)
            ->get();

        // whereIsActive()는 is_active 컬럼이 true인 행만 조회하는 Eloquent 동적 where이며 공개 슬라이드만 노출하기 위해 사용한다
        $sliders = Slider::whereIsActive(true)->get();
        $heroBanner = HeroBanner::first();

        // Eloquent cast로 JSON categories를 PHP 배열로 받고, 설정 행이 없으면 null-safe 연산자와 빈 배열로 안전하게 대체한다
        $popularCategoriesIds = PopularCategory::first()?->categories ?? [];
        $popularCategories = Category::whereIn('id', $popularCategoriesIds)->get();
        $popularProducts = $this->productsByCategory($popularCategoriesIds);

        // withAvg()는 reviews 관계의 rating 평균을 reviews_avg_rating 별칭으로 함께 조회해 홈에서 별도 반복 쿼리를 방지한다
        $flashSale = FlashSale::first();
        $flashSaleProducts = Product::withAvg('reviews', 'rating')
            ->whereIn('id', $flashSale?->products ?? [])
            ->get();

        $productSections = ProductSection::first();
        $productSectionIds = [
            $productSections?->category_one,
            $productSections?->category_two,
            $productSections?->category_three,
        ];

        // with('primaryImage')는 대표 이미지를 eager loading하여 상품마다 이미지 쿼리가 반복되는 N+1 문제를 방지한다
        // latest()는 created_at 내림차순으로 정렬해 최근 등록 상품부터 조회한다
        $hotProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->whereIsHot(true)
            ->latest()
            ->take(4)
            ->get();
        $newProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->whereIsNew(true)
            ->latest()
            ->take(4)
            ->get();
        $featuredProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->whereIsFeatured(true)
            ->latest()
            ->take(4)
            ->get();

        // whereHas()는 reviews 관계가 실제로 존재하는 상품만 고르고, 집계한 평균 평점이 높은 순서로 정렬하게 한다
        $topRatedProducts = Product::with('primaryImage')
            ->whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(4)
            ->get();

        $productSectionsProducts = $this->productsByCategory($productSectionIds, false);

        // view()는 Blade 템플릿을 렌더링하는 Laravel 응답을 만들고 compact() 결과를 뷰 데이터로 전달한다
        return view('user.home.index', compact(
            'featuredCategories',
            'sliders',
            'heroBanner',
            'popularCategories',
            'popularProducts',
            'flashSale',
            'flashSaleProducts',
            'productSectionsProducts',
            'hotProducts',
            'newProducts',
            'featuredProducts',
            'topRatedProducts',
        ));
    }

    // 1. 전달받은 각 Category id로 카테고리를 조회한다
    // 2. 현재 카테고리와 모든 하위 카테고리 id를 하나로 합친다
    // 3. 해당 카테고리에 연결된 추천 상품 또는 최신 상품을 조회한다
    // 4. 최상위 Category id를 키로 사용한 상품 목록 배열을 반환한다
    private function productsByCategory(array $categoryIds, bool $featured = true, int $limit = 12): array
    {
        $results = [];

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);

            if (!$category) {
                continue;
            }

            $ids = array_merge([$category->id], $category->allChildrenIds());

            // whereHas()는 category_product 중간 테이블 관계에 대상 Category id가 있는 상품만 조회하기 위해 사용한다
            $products = Product::withAvg('reviews', 'rating')
                ->whereHas('categories', function ($query) use ($ids) {
                    $query->whereIn('categories.id', $ids);
                });

            if ($featured) {
                // whereIsFeatured()는 원본 홈 인기 카테고리 영역처럼 추천 상품만 최대 12개 조회한다
                $products = $products->whereIsFeatured(true)->take(12)->get();
            } else {
                $products = $products->latest()->take($limit)->get();
            }

            $results[$categoryId] = $products;
        }

        return $results;
    }
}
