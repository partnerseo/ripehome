<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('ad', 100);
            $table->string('soyad', 100);
            $table->string('firma', 200)->nullable();
            $table->string('ulke', 2)->default('TR');
            $table->string('telefon', 20)->unique();
            $table->enum('durum', ['beklemede', 'onaylandi', 'pasif'])->default('beklemede');
            $table->timestamp('banned_at')->nullable();
            $table->timestamps();

            $table->index('durum');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
