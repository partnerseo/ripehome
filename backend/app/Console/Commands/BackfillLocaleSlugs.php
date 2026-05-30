<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillLocaleSlugs extends Command
{
    protected $signature = 'slug:backfill {--force}';
    protected $description = 'slug_en/ar/ru/de üretir: Product/Category Spatie name çevirisinden, BlogPost title_<loc>dan (ASCII, eşsiz)';

    private array $locales = ['en', 'ar', 'ru', 'de'];

    public function handle(): int
    {
        // [model, kaynak tipi] — spatie: getTranslation('name'), cols: title_<loc>
        $this->backfillModel(Product::class, 'spatie', 'name');
        $this->backfillModel(Category::class, 'spatie', 'name');
        $this->backfillModel(BlogPost::class, 'cols', 'title');
        $this->info('Bitti.');
        return self::SUCCESS;
    }

    private function backfillModel(string $model, string $type, string $field): void
    {
        $table = (new $model)->getTable();
        $this->info("== {$table} ({$type}:{$field}) ==");
        $rows = $model::query()->get();

        foreach ($this->locales as $loc) {
            $used = array_flip(
                $model::query()->whereNotNull("slug_{$loc}")->pluck("slug_{$loc}")->all()
            );
            $count = 0;

            foreach ($rows as $row) {
                $col = "slug_{$loc}";
                if (!$this->option('force') && !empty($row->$col)) {
                    continue;
                }
                if ($type === 'spatie') {
                    $source = method_exists($row, 'getTranslation')
                        ? ($row->getTranslation($field, $loc, false) ?: null)
                        : null;
                } else {
                    $source = $row->{"{$field}_{$loc}"} ?: null;
                }
                if (empty($source)) {
                    continue; // çeviri yok → null, Resource TR slug'a düşer
                }
                $base = Str::slug($source);
                if ($base === '') {
                    continue;
                }
                $slug = $base;
                $i = 2;
                while (isset($used[$slug]) && $used[$slug] !== $row->id) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                if (($row->$col ?? null) !== $slug) {
                    $row->$col = $slug;
                    $row->saveQuietly();
                    $count++;
                }
                $used[$slug] = $row->id;
            }
            $this->line("  {$loc}: {$count}");
        }
    }
}
