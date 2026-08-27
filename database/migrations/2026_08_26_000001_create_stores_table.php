<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            // unique()는 User 한 명이 Store 한 건만 갖도록 seller_id 중복을 막고 constrained('users')는 실제 users.id만 참조하게 한다
            $table->foreignId('seller_id')->unique()->constrained('users');
            $table->string('logo')->default('/defaults/shop.png');
            $table->string('banner')->default('/defaults/banner.png');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
