<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 1. users, password_reset_tokens, sessions 테이블을 생성한다
    // 2. 회원가입과 인증에 필요한 컬럼과 기본값을 정의한다
    public function up(): void
    {
        // Schema Facade와 Blueprint를 사용해 users 테이블의 구조를 정의한다
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // 회원가입 시 Buyer와 Seller를 구분하고, 입력하지 않으면 Buyer로 저장한다
            $table->enum('user_type', ['buyer', 'seller'])->default('buyer');
            $table->rememberToken();
            $table->timestamps();
        });

        // 비밀번호 재설정 토큰을 저장할 테이블을 생성한다
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // database session driver가 사용할 세션 저장 테이블을 생성한다
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    // 1. up()에서 생성한 세 테이블을 삭제한다
    // 2. 마이그레이션을 되돌릴 때 데이터베이스 구조를 이전 상태로 복구한다
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
