<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // member_tokens: token_prefix ekle (O(1) lookup için)
        Schema::table('member_tokens', function (Blueprint $table) {
            $table->string('token_prefix', 16)->nullable()->index()->after('id');
        });

        // Eski token'ları sil (prefix'siz, lookup yapılamaz)
        \DB::table('member_tokens')->delete();

        // members: email ekle (sipariş bildirimi için)
        Schema::table('members', function (Blueprint $table) {
            $table->string('email')->nullable()->after('ulke');
        });
    }

    public function down(): void
    {
        Schema::table('member_tokens', function (Blueprint $table) {
            $table->dropIndex(['token_prefix']);
            $table->dropColumn('token_prefix');
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
