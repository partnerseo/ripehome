<x-filament-panels::page>

@php $products = $this->getProducts(); @endphp

<style>
.mi-header   { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.mi-badge    { background:#ef4444; color:#fff; border-radius:20px; padding:4px 14px; font-size:14px; font-weight:700; }
.mi-done     { background:#10b981; color:#fff; border-radius:20px; padding:4px 14px; font-size:14px; font-weight:700; }
.mi-search   { flex:1; min-width:200px; max-width:300px; padding:8px 12px; border:1px solid #d1d5db;
               border-radius:8px; font-size:13px; outline:none; }
.mi-refresh  { padding:8px 16px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:8px;
               font-size:13px; font-weight:500; cursor:pointer; color:#374151; white-space:nowrap; }
.mi-refresh:hover { background:#e5e7eb; }
.mi-hint     { font-size:11px; color:#9ca3af; }

.mi-grid     { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:12px; }
.mi-card     { background:#fff; border:1px solid #fecaca; border-radius:12px; padding:16px;
               display:flex; flex-direction:column; gap:10px; }
.mi-cat      { font-size:10px; color:#ef4444; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.mi-name     { font-size:14px; color:#111; font-weight:600; line-height:1.3; }
.mi-actions  { display:flex; gap:6px; }
.mi-upload   { flex:2; display:block; text-align:center; background:#3b82f6; color:#fff;
               border-radius:8px; padding:9px 0; font-size:13px; font-weight:600;
               text-decoration:none; transition:background .15s; }
.mi-upload:hover { background:#2563eb; }
.mi-check    { flex:1; display:block; text-align:center; background:#10b981; color:#fff;
               border:none; border-radius:8px; padding:9px 0; font-size:13px; font-weight:600;
               cursor:pointer; transition:background .15s; white-space:nowrap; }
.mi-check:hover  { background:#059669; }
.mi-empty    { text-align:center; padding:60px 0; color:#6b7280; font-size:16px; }
</style>

<div class="mi-header">
    @if(count($products) > 0)
        <span class="mi-badge">{{ count($products) }} ürün resimsiz</span>
    @else
        <span class="mi-done">Tüm ürünlerin resmi var ✓</span>
    @endif

    <input type="text"
           class="mi-search"
           placeholder="Ürün adı ara..."
           wire:model.live.debounce.300ms="search">

    <button class="mi-refresh" wire:click="refresh">
        ↻ Listeyi Yenile
    </button>

    <span class="mi-hint">Resim yükle → kaydet → ✓ butonuna bas</span>
</div>

@if(count($products) === 0)
    <div class="mi-empty">Tüm ürünlerin resmi var! 🎉</div>
@else
    <div class="mi-grid">
        @foreach($products as $p)
            <div class="mi-card">
                <div>
                    <div class="mi-cat">{{ $p['category'] }}</div>
                    <div class="mi-name">{{ $p['name'] }}</div>
                </div>
                <div class="mi-actions">
                    <a href="{{ $p['edit_url'] }}" target="_blank" class="mi-upload">
                        📷 Resim Yükle
                    </a>
                    <button class="mi-check" wire:click="check({{ $p['id'] }})">
                        ✓
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

</x-filament-panels::page>
