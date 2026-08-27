<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 1. Category와 Product의 다대다 연결을 저장할 category_product 중간 테이블을 만든다
    // 2. 카테고리나 상품이 삭제되면 그 연결도 함께 삭제한다
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();

            // onDelete('cascade')는 연결 대상 Category 또는 Product가 삭제되면 이 중간 테이블 행도 자동 삭제하게 한다
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    // 1. Migration을 되돌릴 때 category_product 중간 테이블을 삭제한다
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
