<x-filament-panels::page>

@php
    $orphans  = $this->getOrphanImages();
    $products = $this->getEmptyProducts();
@endphp

<style>
.ia-wrap      { display:flex; gap:12px; height: calc(100vh - 160px); }
.ia-gallery   { flex:1; overflow-y:auto; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:10px; }
.ia-sidebar   { width:340px; display:flex; flex-direction:column; gap:8px; overflow:hidden; }
.ia-grid      { display:grid; grid-template-columns: repeat(auto-fill, minmax(120px,1fr)); gap:6px; }

.ia-img-card  { position:relative; cursor:pointer; border-radius:7px; overflow:hidden;
                border:3px solid transparent; transition:border-color .15s; background:#e5e7eb; }
.ia-img-card:hover          { border-color:#94a3b8; }
.ia-img-card.selected       { border-color:#10b981; box-shadow:0 0 0 2px #10b981; }
.ia-img-card img            { width:100%; aspect-ratio:1; object-fit:cover; display:block; }
.ia-img-card .badge         { position:absolute; top:3px; left:3px; background:rgba(0,0,0,.6);
                               color:#fff; font-size:9px; padding:1px 5px; border-radius:3px; }
.ia-img-card.selected .badge{ background:#10b981; }

.ia-topbar    { display:flex; align-items:center; gap:10px; margin-bottom:10px; flex-wrap:wrap; }
.ia-sel-badge { background:#10b981; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:600; }
.ia-btn       { padding:5px 14px; border-radius:6px; border:none; cursor:pointer; font-size:13px; font-weight:500; }
.ia-btn-gray  { background:#e5e7eb; color:#374151; }

.ia-panel     { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:12px;
                flex:1; display:flex; flex-direction:column; overflow:hidden; }
.ia-search    { width:100%; padding:6px 8px; border:1px solid #d1d5db; border-radius:6px;
                font-size:12px; margin-bottom:8px; box-sizing:border-box; }
.ia-plist     { overflow-y:auto; display:flex; flex-direction:column; gap:3px; flex:1; }
.ia-stats     { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px; font-size:12px; }
.ia-empty     { text-align:center; color:#9ca3af; padding:40px 0; font-size:14px; }
.tab-btn      { flex:1; padding:7px; border:none; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; }

/* Düzelt: mini resim grid */
.dz-img-wrap  { position:relative; display:inline-block; }
.dz-img-wrap img { width:54px; height:54px; object-fit:cover; border-radius:5px; display:block; }
.dz-remove    { position:absolute; top:-4px; right:-4px; width:16px; height:16px;
                background:#ef4444; color:#fff; border:none; border-radius:50%;
                font-size:10px; line-height:16px; text-align:center; cursor:pointer;
                padding:0; display:flex; align-items:center; justify-content:center; }
</style>

<div class="ia-stats mb-4">
    <strong>{{ count($orphans) }}</strong> sahipsiz resim &nbsp;|&nbsp;
    <strong>{{ count($products) }}</strong> resimsiz ürün
    @if($assignedCount)
        &nbsp;|&nbsp; <span style="color:#10b981">Son atama: {{ $assignedCount }} resim ✓</span>
    @endif
</div>

<div class="ia-wrap">

    {{-- SOL: Resim Galerisi --}}
    <div class="ia-gallery">
        <div class="ia-topbar">
            <span class="ia-sel-badge">{{ count($selectedImages) }} seçili</span>
            <button class="ia-btn ia-btn-gray" wire:click="clearSelection">Temizle</button>
            <input type="text" class="ia-search" style="width:200px;margin:0"
                   placeholder="Dosya ara..." wire:model.live.debounce.400ms="imageSearch">
            <span style="font-size:12px;color:#6b7280">Resme tıkla = seç/bırak</span>
        </div>

        @if(count($orphans) === 0)
            <div class="ia-empty">Tüm resimler atanmış! 🎉</div>
        @else
            <div class="ia-grid">
                @foreach($orphans as $i => $file)
                    @php $isSelected = in_array($file, $selectedImages); @endphp
                    <div class="ia-img-card {{ $isSelected ? 'selected' : '' }}"
                         wire:click="toggleImage('{{ $file }}')"
                         title="{{ basename($file) }}">
                        <img src="{{ Storage::disk('public')->url($file) }}"
                             loading="lazy"
                             onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>?</text></svg>'">
                        <div class="badge">{{ $i + 1 }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SAĞ: Sidebar --}}
    <div class="ia-sidebar">

        {{-- Sekmeler --}}
        <div style="display:flex;gap:4px;flex-shrink:0;flex-wrap:wrap;">
            <button wire:click="switchTab('ata')"
                style="background:{{ $activeTab === 'ata' ? '#10b981' : '#e5e7eb' }};color:{{ $activeTab === 'ata' ? '#fff' : '#374151' }};"
                class="tab-btn">
                Ata ({{ count($products) }})
            </button>
            <button wire:click="switchTab('eksik')"
                style="background:{{ $activeTab === 'eksik' ? '#ef4444' : '#e5e7eb' }};color:{{ $activeTab === 'eksik' ? '#fff' : '#374151' }};"
                class="tab-btn">
                Resimsiz Ürünler ({{ count($this->getMissingProducts()) }})
            </button>
            <button wire:click="switchTab('duzelt')"
                style="background:{{ $activeTab === 'duzelt' ? '#f59e0b' : '#e5e7eb' }};color:{{ $activeTab === 'duzelt' ? '#fff' : '#374151' }};"
                class="tab-btn">
                Yanlış Atamayı Düzelt
            </button>
        </div>

        {{-- ATA SEKMESİ --}}
        @if($activeTab === 'ata')
        <div class="ia-panel">
            @if(count($selectedImages) > 0)
                <div style="background:#ecfdf5;border:1px solid #10b981;border-radius:6px;padding:8px;margin-bottom:8px;font-size:12px;">
                    <strong style="color:#10b981">{{ count($selectedImages) }} resim seçili</strong> — ürüne tıkla, direk atar.
                </div>
            @else
                <div style="background:#fef9c3;border:1px solid #fde047;border-radius:6px;padding:8px;margin-bottom:8px;font-size:12px;color:#713f12;">
                    Soldan resim seç, sonra ürüne tıkla → direk atanır.
                </div>
            @endif

            <input type="text" class="ia-search"
                   placeholder="Ürün ara..."
                   wire:model.live.debounce.300ms="productSearch">

            <div class="ia-plist">
                @forelse($products as $p)
                    <div wire:click="{{ count($selectedImages) > 0 ? 'assignToProduct('.$p['id'].')' : '' }}"
                         style="display:flex;justify-content:space-between;align-items:center;
                                padding:7px 10px;
                                border:1px solid {{ count($selectedImages) > 0 ? '#10b981' : '#e5e7eb' }};
                                border-radius:6px;
                                background:{{ count($selectedImages) > 0 ? '#f0fdf4' : '#f9fafb' }};
                                cursor:{{ count($selectedImages) > 0 ? 'pointer' : 'default' }};
                                transition:all .15s;
                                {{ count($selectedImages) === 0 ? 'opacity:.55;' : '' }}">
                        <div style="flex:1;min-width:0;">
                            @if($p['category'])
                                <div style="font-size:9px;color:#10b981;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:1px;">{{ $p['category'] }}</div>
                            @endif
                            <div style="font-size:12px;color:#111;">{{ $p['name'] }}</div>
                        </div>
                        @if(count($selectedImages) > 0)
                            <span style="font-size:10px;color:#10b981;font-weight:700;margin-left:6px;">→ Ata</span>
                        @endif
                    </div>
                @empty
                    <div class="ia-empty" style="padding:20px">Tüm ürünlerin resmi var! 🎉</div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- RESİMSİZ ÜRÜNLER SEKMESİ --}}
        @if($activeTab === 'eksik')
        <div class="ia-panel">
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px;margin-bottom:8px;font-size:11px;color:#991b1b;">
                Bu ürünlerin resmi yok. <strong>Düzenle</strong> butonuna bas → resim yükle → kaydet.
            </div>
            <input type="text" class="ia-search"
                   placeholder="Ürün ara..."
                   wire:model.live.debounce.300ms="missingSearch">
            <div class="ia-plist">
                @forelse($this->getMissingProducts() as $p)
                    <div style="display:flex;justify-content:space-between;align-items:center;
                                padding:8px 10px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:9px;color:#ef4444;font-weight:700;text-transform:uppercase;margin-bottom:1px;">{{ $p['category'] }}</div>
                            <div style="font-size:12px;color:#111;font-weight:500;">{{ $p['name'] }}</div>
                        </div>
                        <a href="{{ $p['edit_url'] }}"
                           target="_blank"
                           style="font-size:11px;background:#3b82f6;color:#fff;border:none;border-radius:5px;
                                  padding:4px 10px;cursor:pointer;white-space:nowrap;margin-left:8px;
                                  text-decoration:none;display:inline-block;">
                            Düzenle →
                        </a>
                    </div>
                @empty
                    <div class="ia-empty" style="padding:20px">Tüm ürünlerin resmi var! 🎉</div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- DÜZELT SEKMESİ --}}
        @if($activeTab === 'duzelt')
        <div class="ia-panel">
            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:6px;padding:8px;margin-bottom:8px;font-size:11px;color:#92400e;">
                Yanlış atanan resmin üstündeki <strong>✕</strong> butonuna bas → sadece o resim kaldırılır.
            </div>
            <input type="text" class="ia-search"
                   placeholder="Ürün ara..."
                   wire:model.live.debounce.300ms="productSearch">
            <div class="ia-plist">
                @forelse($this->getAssignedProducts() as $p)
                    <div style="border:1px solid #e5e7eb;border-radius:8px;padding:8px;background:#f9fafb;">
                        <div style="margin-bottom:6px;">
                            @if($p['category'])
                                <div style="font-size:9px;color:#6b7280;font-weight:700;text-transform:uppercase;">{{ $p['category'] }}</div>
                            @endif
                            <div style="font-size:12px;color:#111;font-weight:600;">{{ $p['name'] }}</div>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            @foreach($p['images'] as $img)
                                <div class="dz-img-wrap">
                                    <img src="{{ Storage::disk('public')->url($img) }}"
                                         onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2254%22 height=%2254%22><rect fill=%22%23e5e7eb%22 width=%2254%22 height=%2254%22/></svg>'">
                                    <button class="dz-remove"
                                            wire:click="removeImageFromProduct({{ $p['id'] }}, '{{ $img }}')"
                                            wire:confirm="Bu resmi üründen kaldır?"
                                            title="Kaldır">✕</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="ia-empty" style="padding:20px">Henüz atama yapılmamış.</div>
                @endforelse
            </div>
        </div>
        @endif

    </div>
</div>

</x-filament-panels::page>
