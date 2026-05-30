<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // otp_codes: telefon artık nullable (email identifier olacak)
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->string('telefon', 20)->nullable()->change();
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->string('telefon', 20)->nullable(false)->change();
        });
    }
};
