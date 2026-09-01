<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ChecklistItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    /** @use HasFactory<ChecklistItemFactory> */
    use HasFactory;

    public const GROUPS = ['anne', 'bebek', 'belgeler'];

    protected $fillable = ['template_key', 'title', 'group', 'is_done', 'sort'];

    protected $attributes = ['group' => 'anne', 'is_done' => false];

    protected function casts(): array
    {
        return ['is_done' => 'boolean', 'sort' => 'integer'];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }
}
