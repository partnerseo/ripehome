<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('anahtar', 100);
            $table->string('tip', 20); // 'phone' | 'ip'
            $table->unsignedSmallInteger('istek_sayisi')->default(0);
            $table->timestamp('pencere_baslangici');
            $table->timestamps();

            $table->unique(['anahtar', 'tip']);
            $table->index('pencere_baslangici');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_rate_limits');
    }
};
