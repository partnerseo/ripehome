<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkImportDraft extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'auto_publish',
        'products',
    ];

    protected $casts = [
        'products' => 'array',
        'auto_publish' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
