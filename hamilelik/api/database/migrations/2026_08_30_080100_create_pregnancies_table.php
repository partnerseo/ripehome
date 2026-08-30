<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pregnancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Kullanıcının girdiği ham veri — düzeltme geri alınabilsin diye saklanır.
            $table->string('method', 16);
            $table->date('input_date');
            $table->unsignedTinyInteger('cycle_length')->default(28);

            // Motorun türettiği değerler; sorgularda tekrar hesaplamamak için yazılır.
            $table->date('lmp_date');
            $table->date('due_date');

            $table->unsignedTinyInteger('baby_count')->default(1);

            $table->string('status', 16)->default('active');
            $table->timestamp('ended_at')->nullable();
            $table->string('ended_reason', 16)->nullable();

            // Kullanıcı başına tek aktif gebelik. MySQL kısmi index desteklemediği
            // için aktifken 1, aksi hâlde NULL olan bir bayrak üzerinden zorlanır;
            // NULL'lar unique kısıtını tetiklemez, yani kapalı gebelik sınırsız.
            $table->unsignedTinyInteger('active_flag')->nullable();
            $table->unique(['user_id', 'active_flag']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancies');
    }
};
