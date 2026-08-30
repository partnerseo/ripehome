<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tetkik takvimi kaynağı. Gebelik oluşturulduğunda buradan otomatik
        // randevu kayıtları üretilecek (Sprint 4).
        Schema::create('screening_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64);
            $table->string('locale', 5)->default('tr');
            $table->string('country', 2)->default('TR');

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 16);
            $table->unsignedTinyInteger('week_start');
            $table->unsignedTinyInteger('week_end');
            $table->boolean('is_optional')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);

            $table->string('status', 16)->default('draft');
            $table->string('reviewed_by')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->json('source_refs')->nullable();

            $table->timestamps();

            $table->unique(['code', 'locale', 'country']);
            $table->index(['country', 'locale', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_templates');
    }
};
