<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('telefon', 20)->index();
            $table->string('kod_hash', 255);
            $table->timestamp('son_kullanma');
            $table->unsignedTinyInteger('deneme_sayisi')->default(0);
            $table->string('ip_adresi', 45)->nullable();
            $table->boolean('kullanildi_mi')->default(false);
            $table->timestamps();

            $table->index(['telefon', 'kullanildi_mi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
