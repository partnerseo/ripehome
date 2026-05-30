<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('featured_products', function (Blueprint $table) {
            // Eski pivot kolonu varsa kaldır (önce FK constraint)
            if (Schema::hasColumn('featured_products', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            // Yeni kolonları ekle (yoksa)
            if (!Schema::hasColumn('featured_products', 'category_label')) {
                $table->string('category_label')->nullable()->after('id');
            }
            if (!Schema::hasColumn('featured_products', 'title')) {
                $table->string('title')->after('category_label');
            }
            if (!Schema::hasColumn('featured_products', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('featured_products', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('featured_products', 'tags')) {
                $table->json('tags')->nullable()->after('image');
            }
            if (!Schema::hasColumn('featured_products', 'button_text')) {
                $table->string('button_text')->default('Detayları Gör')->after('tags');
            }
            if (!Schema::hasColumn('featured_products', 'button_link')) {
                $table->string('button_link')->nullable()->after('button_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('featured_products', function (Blueprint $table) {
            $table->dropColumn(['category_label', 'title', 'description', 'image', 'tags', 'button_text', 'button_link']);
            $table->unsignedBigInteger('product_id')->nullable();
        });
    }
};
