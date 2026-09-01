<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eşin salt-okunur erişimi. Davet e-postayla gider, jetonla kabul edilir.
        Schema::create('pregnancy_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->string('invited_email');
            // Kabul edilene kadar boş; kabul eden kullanıcı buraya bağlanır.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role', 16)->default('viewer');
            $table->string('token', 64)->unique();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['pregnancy_id', 'invited_email']);
        });

        // Karın fotoğrafı zaman tüneli. Dosyalar herkese açık diskte değil:
        // sağlık verisi, yalnızca yetkili istekle servis edilir.
        Schema::create('belly_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('week');
            $table->string('path');
            $table->date('taken_on');
            $table->timestamps();

            $table->index(['pregnancy_id', 'week']);
        });

        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->string('template_key', 64)->nullable();
            $table->string('title');
            $table->string('group', 32)->default('anne');
            $table->boolean('is_done')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['pregnancy_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('belly_photos');
        Schema::dropIfExists('pregnancy_shares');
    }
};
