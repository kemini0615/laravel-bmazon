<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // store_id와 brand_id는 이미 만든 stores, brands 테이블의 실제 id만 참조한다
            $table->foreignId('store_id')->constrained('stores');
            $table->enum('product_type', ['physical', 'digital'])->nullable();
            $table->foreignId('brand_id')->constrained('brands');
            $table->string('name');
            $table->string('slug');
            $table->double('price')->nullable();
            $table->longText('description');
            $table->text('short_description')->nullable();
            $table->double('special_price')->nullable();
            $table->date('special_price_start')->nullable();
            $table->date('special_price_end')->nullable();
            $table->string('sku')->nullable();
            $table->enum('manage_stock', ['yes', 'no'])->nullable();
            $table->integer('qty')->nullable();
            $table->boolean('in_stock')->nullable();
            $table->integer('viewed')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->nullable();
            $table->enum('approved_status', ['approved', 'pending', 'rejected'])->default('pending')->nullable();
            $table->boolean('is_featured')->nullable();
            $table->boolean('is_hot')->nullable();
            $table->boolean('is_new')->nullable();

            // softDeletesDatetime()는 deleted_at을 추가해 DELETE 대신 삭제 시각을 기록하게 한다
            $table->softDeletesDatetime();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
