<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['products', 'categories', 'blog_posts'];
    private array $locales = ['en', 'ar', 'ru', 'de'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach ($this->locales as $loc) {
                    $col = "slug_{$loc}";
                    if (!Schema::hasColumn($table, $col)) {
                        $t->string($col)->nullable()->after('slug');
                        $t->unique($col, "{$table}_{$col}_unique");
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach ($this->locales as $loc) {
                    $col = "slug_{$loc}";
                    if (Schema::hasColumn($table, $col)) {
                        $t->dropUnique("{$table}_{$col}_unique");
                        $t->dropColumn($col);
                    }
                }
            });
        }
    }
};
