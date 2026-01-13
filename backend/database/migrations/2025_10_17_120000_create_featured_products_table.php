<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_products', function (Blueprint $table) {
            $table->id();
            $table->string('category_label')->nullable(); // "PREMIUM SPA DENEYİMİ"
            $table->string('title'); // "Özel Dokuma Waffle Bornoz"
            $table->text('description'); // Açıklama
            $table->string('image'); // Ana görsel
            $table->json('tags')->nullable(); // ["%100 Pamuk", "Yüksek Emicilik"]
            $table->string('button_text')->default('Detayları Gör');
            $table->string('button_link'); // Link
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_products');
    }
};












