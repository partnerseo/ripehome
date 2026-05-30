<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesaleOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'tax_office',
        'tax_number',
        'items',
        'additional_notes',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'items' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Toplam ürün sayısını hesapla
     */
    public function getTotalItemsAttribute(): int
    {
        return count($this->items);
    }

    /**
     * Toplam adet sayısını hesapla
     */
    public function getTotalQuantityAttribute(): int
    {
        return collect($this->items)->sum('quantity');
    }

    /**
     * Durum badge rengini al
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'reviewing' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Durum etiketini al (Türkçe)
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Yeni Talep',
            'reviewing' => 'İnceleniyor',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'completed' => 'Tamamlandı',
            default => $this->status,
        };
    }
}





