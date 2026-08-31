<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kilo, tansiyon, kan şekeri tek tabloda: üçü de "bir gün, bir-iki sayı".
        Schema::create('health_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->string('type', 16);
            $table->decimal('value_1', 6, 2);
            $table->decimal('value_2', 6, 2)->nullable();
            $table->string('unit', 12);
            $table->date('measured_on');
            $table->text('note')->nullable();
            $this->offlineKey($table);
            $table->timestamps();

            $table->index(['pregnancy_id', 'type', 'measured_on']);
        });

        Schema::create('kick_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedTinyInteger('target_count')->default(10);
            $table->unsignedTinyInteger('kick_count')->default(0);
            $this->offlineKey($table);
            $table->timestamps();

            $table->index(['pregnancy_id', 'started_at']);
        });

        Schema::create('kick_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kick_session_id')->constrained()->cascadeOnDelete();
            $table->dateTime('occurred_at');
        });

        Schema::create('contraction_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $this->offlineKey($table);
            $table->timestamps();

            $table->index(['pregnancy_id', 'started_at']);
        });

        Schema::create('contractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contraction_session_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->unsignedSmallInteger('duration_sec');
            // Bir önceki kasılmanın başlangıcından bu kasılmanın başlangıcına.
            $table->unsignedSmallInteger('interval_sec')->nullable();
        });

        Schema::create('symptom_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->date('logged_on');
            $table->json('symptoms')->nullable();
            $table->unsignedTinyInteger('mood')->nullable();
            $table->text('note')->nullable();
            $this->offlineKey($table);
            $table->timestamps();

            $table->index(['pregnancy_id', 'logged_on']);
        });
    }

    /**
     * Çevrimdışı yazılan her kayıt cihazda üretilmiş bir anahtar taşır.
     * Aynı anahtar iki kez gönderilirse ikinci gönderim kopya üretmez —
     * bağlantı koptuğunda istemci güvenle tekrar dener.
     */
    private function offlineKey(Blueprint $table): void
    {
        $table->uuid('client_uuid')->nullable();
        $table->unique(['pregnancy_id', 'client_uuid']);
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_logs');
        Schema::dropIfExists('contractions');
        Schema::dropIfExists('contraction_sessions');
        Schema::dropIfExists('kick_events');
        Schema::dropIfExists('kick_sessions');
        Schema::dropIfExists('health_logs');
    }
};
