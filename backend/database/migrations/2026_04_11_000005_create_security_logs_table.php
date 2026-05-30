<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            // login | failed_otp | otp_locked | token_revoked | banned | registered | logout
            $table->string('olay', 50);
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'created_at']);
            $table->index(['olay', 'created_at']);
            $table->index('ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
