<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 255)->unique();
            $table->timestamp('son_kullanma');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'son_kullanma']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_tokens');
    }
};
