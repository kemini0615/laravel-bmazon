<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_one')->nullable();
            $table->foreignId('category_two')->nullable();
            $table->foreignId('category_three')->nullable();
            $table->timestamps();
        });
    }

    // 1. Migration을 되돌릴 때 product_sections 테이블을 삭제한다
    public function down(): void
    {
        Schema::dropIfExists('product_sections');
    }
};
