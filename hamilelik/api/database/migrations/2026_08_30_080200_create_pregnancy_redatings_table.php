<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USG ile yeniden tarihleme. Orijinal giriş pregnancies tablosunda durur;
        // buradaki kayıt silinince hesap eski hâline döner.
        Schema::create('pregnancy_redatings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained()->cascadeOnDelete();
            $table->date('measured_on');
            $table->unsignedSmallInteger('ga_days_at_measure');
            $table->string('source', 16)->default('usg');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['pregnancy_id', 'measured_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_redatings');
    }
};
