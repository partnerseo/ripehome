<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screening_template_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('category', 16);
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(false);

            // Tetkiğin geçerli olduğu aralık, gebeliğin kendi SAT'ından türetilir.
            // Tarih olarak saklanır ki takvim ekranı yeniden hesaplamasın.
            $table->unsignedTinyInteger('window_start_week')->nullable();
            $table->unsignedTinyInteger('window_end_week')->nullable();
            $table->date('window_start_on')->nullable();
            $table->date('window_end_on')->nullable();

            // Kullanıcının aldığı gerçek randevu.
            $table->dateTime('scheduled_at')->nullable();
            $table->string('location')->nullable();
            $table->string('doctor_name')->nullable();
            $table->text('notes')->nullable();

            $table->dateTime('reminder_at')->nullable();
            $table->dateTime('reminded_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->string('source', 8)->default('auto');

            $table->timestamps();

            // Bir tetkik bir gebelikte yalnızca bir kez üretilir.
            $table->unique(['pregnancy_id', 'screening_template_id']);
            $table->index(['pregnancy_id', 'window_start_on']);
            $table->index('reminder_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
