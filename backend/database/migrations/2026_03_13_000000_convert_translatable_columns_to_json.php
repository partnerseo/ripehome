<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mevcut string değerleri {"tr": "değer"} JSON formatına çevirir.
     */
    public function up(): void
    {
        $tables = [
            'products' => ['name', 'description', 'short_description', 'meta_title', 'meta_description'],
            'categories' => ['name', 'description'],
            'pages' => ['title', 'content', 'meta_title', 'meta_description'],
            'home_sliders' => ['title', 'subtitle', 'button_text'],
            'featured_products' => ['category_label', 'title', 'description', 'button_text'],
            'featured_sections' => ['title', 'description', 'button_text'],
            'settings' => ['footer_text', 'brand_title', 'brand_subtitle', 'brand_description', 'brand_button_text'],
            'tags' => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->$column ?? null;

                    // Zaten JSON formatında mı kontrol et
                    if ($value !== null && !$this->isJson($value)) {
                        $updates[$column] = json_encode(['tr' => $value], JSON_UNESCAPED_UNICODE);
                    }
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    /**
     * JSON formatındaki değerleri tekrar düz string'e çevirir.
     */
    public function down(): void
    {
        $tables = [
            'products' => ['name', 'description', 'short_description', 'meta_title', 'meta_description'],
            'categories' => ['name', 'description'],
            'pages' => ['title', 'content', 'meta_title', 'meta_description'],
            'home_sliders' => ['title', 'subtitle', 'button_text'],
            'featured_products' => ['category_label', 'title', 'description', 'button_text'],
            'featured_sections' => ['title', 'description', 'button_text'],
            'settings' => ['footer_text', 'brand_title', 'brand_subtitle', 'brand_description', 'brand_button_text'],
            'tags' => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->$column ?? null;

                    if ($value !== null && $this->isJson($value)) {
                        $decoded = json_decode($value, true);
                        $updates[$column] = $decoded['tr'] ?? $value;
                    }
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE && str_starts_with(trim($string), '{');
    }
};
