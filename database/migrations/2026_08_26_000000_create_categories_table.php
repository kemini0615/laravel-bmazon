<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // foreignId()는 다른 테이블의 id를 저장하는 BIGINT 컬럼을 만든다
            // nullable()은 최상위 카테고리가 부모 없이 저장될 수 있게 한다
            // constrained('categories')는 parent_id가 categories 테이블에 실제로 존재하는 id만 가리키도록 DB 외래 키 제약을 만든다
            $table->foreignId('parent_id')->nullable()->constrained('categories');

            $table->string('name');
            $table->string('slug');
            $table->integer('position');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_featured')->nullable();
            $table->boolean('is_active')->default(1);

            // timestamps()는 created_at과 updated_at 컬럼을 만들어 생성·수정 시각을 Laravel이 기록하게 한다
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
