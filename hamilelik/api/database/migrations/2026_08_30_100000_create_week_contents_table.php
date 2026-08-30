<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('week_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('week');
            $table->string('locale', 5)->default('tr');

            $table->string('baby_size_label')->nullable();
            $table->unsignedSmallInteger('baby_length_mm')->nullable();
            $table->unsignedSmallInteger('baby_weight_g')->nullable();

            $table->text('baby_body')->nullable();
            $table->text('mother_body')->nullable();
            $table->text('tips_body')->nullable();

            // Tıbbi gözden geçirme kaydı. Yayına çıkmanın ön koşulu.
            $table->string('status', 16)->default('draft');
            $table->string('reviewed_by')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            // Her iddianın dayanağı: [{ "label": "...", "url": "..." }]
            $table->json('source_refs')->nullable();

            $table->timestamps();

            $table->unique(['week', 'locale']);
            $table->index(['locale', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('week_contents');
    }
};
