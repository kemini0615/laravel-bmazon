<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('banner_one');
            $table->string('title_one');
            $table->string('btn_url_one');
            $table->string('banner_two');
            $table->string('title_two');
            $table->string('btn_url_two');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
