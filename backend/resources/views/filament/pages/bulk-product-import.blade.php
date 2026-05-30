<x-filament-panels::page>
    <style>
        .field-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgb(107 114 128);
            margin-bottom: 0.375rem;
            display: block;
        }
        .dark .field-label {
            color: rgb(156 163 175);
        }
        .bulk-card {
            border-left: 3px solid transparent;
        }
        .bulk-card.has-data {
            border-left-color: rgb(245 158 11);
        }
    </style>

    {{-- TASLAK BAR --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-3 mb-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                @if($activeDraftId)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 px-2.5 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ collect($this->drafts)->firstWhere('id', $activeDraftId)?->name ?? 'Taslak' }}
                    </span>
                @else
                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Taslak yok</span>
                @endif
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Taslakları Yükle --}}
                <button type="button"
                        wire:click="openLoadModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Taslaklar
                    @if($this->drafts->count() > 0)
                        <span class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $this->drafts->count() }}</span>
                    @endif
                </button>

                {{-- Hızlı Kaydet / Taslak Kaydet --}}
                <button type="button"
                        wire:click="quickSaveDraft"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors
                            {{ $activeDraftId ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    {{ $activeDraftId ? 'Güncelle' : 'Taslak Kaydet' }}
                </button>

                {{-- Farklı Kaydet --}}
                @if($activeDraftId)
                    <button type="button"
                            wire:click="openSaveDraftModal"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Farklı Kaydet
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- KATEGORİ SEÇİMİ --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="catSelect" class="field-label">Kategori</label>
                <select id="catSelect"
                        wire:model.live="categoryId"
                        class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500 py-2">
                    <option value="">-- Kategori seçin --</option>
                    @foreach($this->categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="inline-flex items-center cursor-pointer gap-2 pb-1">
                <input type="checkbox" wire:model.live="autoPublish" class="sr-only peer">
                <div class="w-10 h-5 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:bg-amber-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all relative"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Otomatik Yayınla</span>
            </label>
        </div>
    </div>

    {{-- TÜMÜNÜ KAYDET BUTONU --}}
    <div class="mb-4">
        <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                style="display:flex !important; width:100%; align-items:center; justify-content:center; gap:0.5rem; padding:0.75rem 1.5rem; border-radius:0.75rem; background-color:rgb(245 158 11); color:white; font-weight:700; font-size:0.95rem; cursor:pointer; border:none;">
            <svg wire:loading wire:target="save" class="animate-spin" style="height:1.25rem;width:1.25rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="save">TÜMÜNÜ KAYDET ({{ $this->filledCount }} ürün, {{ $this->totalImages }} görsel)</span>
            <span wire:loading wire:target="save">Kaydediliyor...</span>
        </button>
    </div>

    {{-- ÜRÜN KARTLARI --}}
    <div class="space-y-3">
        @foreach($products as $index => $product)
            @php
                $hasData = !empty(trim($product['name'] ?? ''));
                $imageCount = count($product['images'] ?? []);
                $isReady = $hasData && $imageCount > 0;
            @endphp
            <div class="bulk-card {{ $hasData ? 'has-data' : '' }} rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden"
                 x-data="{ open: {{ $index < 3 ? 'true' : 'false' }} }">

                {{-- BAŞLIK --}}
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $hasData ? 'bg-amber-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                            {{ $index + 1 }}
                        </span>

                        <span class="font-medium text-sm truncate {{ $hasData ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $hasData ? $product['name'] : 'Boş ürün kartı' }}
                        </span>

                        @if($imageCount > 0)
                            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                {{ $imageCount }} görsel
                            </span>
                        @endif

                        @if($isReady)
                            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                Hazır
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        <span wire:click.stop="removeProduct({{ $index }})"
                              wire:confirm="Bu kartı silmek istediğinize emin misiniz?"
                              class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer">
                            <x-heroicon-m-trash class="w-4 h-4" />
                        </span>
                        <x-heroicon-m-chevron-down class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                   ::class="open ? 'rotate-180' : ''" />
                    </div>
                </button>

                {{-- İÇERİK --}}
                <div x-show="open" x-cloak>
                    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            {{-- FORM --}}
                            <div class="lg:col-span-7 space-y-3">
                                <div>
                                    <label class="field-label">Ürün Adı <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           wire:model.blur="products.{{ $index }}.name"
                                           placeholder="Örn: Premium Pamuk Nevresim Takımı"
                                           class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label class="field-label">Kısa Açıklama</label>
                                    <textarea wire:model.blur="products.{{ $index }}.description"
                                              rows="2"
                                              placeholder="Ürün hakkında kısa bilgi..."
                                              class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500 resize-none"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="field-label">Ürün Kodu (SKU)</label>
                                        <input type="text"
                                               wire:model.blur="products.{{ $index }}.sku"
                                               placeholder="Örn: NVR-001"
                                               class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500 font-mono">
                                    </div>
                                    <div class="flex items-end pb-1">
                                        <label class="inline-flex items-center cursor-pointer gap-2">
                                            <input type="checkbox"
                                                   wire:model.live="products.{{ $index }}.featured"
                                                   class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 rounded-full peer peer-checked:bg-amber-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all relative"></div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Öne Çıkan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- GÖRSEL YÜKLEME --}}
                            <div class="lg:col-span-5">
                                <label class="field-label">Görseller</label>
                                <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed rounded-xl cursor-pointer border-gray-300 dark:border-gray-600 hover:border-amber-400 hover:bg-amber-50/30 dark:hover:bg-amber-900/10 relative">
                                    <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-gray-400 mb-0.5" />
                                    <span class="text-xs text-gray-500">Tıkla veya Sürükle</span>
                                    <input type="file"
                                           wire:model="fileUploads.{{ $index }}"
                                           multiple
                                           accept="image/*"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </label>

                                @if(!empty($product['images']))
                                    <div class="flex flex-wrap gap-1.5 mt-2 max-h-20 overflow-y-auto">
                                        @foreach($product['images'] as $imgIndex => $imgPath)
                                            <div class="relative group w-10 h-10 rounded overflow-hidden border border-gray-200 dark:border-gray-700 flex-shrink-0">
                                                <img src="{{ Storage::disk('public')->url($imgPath) }}"
                                                     alt="{{ $imgIndex + 1 }}"
                                                     class="w-full h-full object-cover">
                                                <button type="button"
                                                        wire:click="removeImage({{ $index }}, {{ $imgIndex }})"
                                                        class="absolute inset-0 flex items-center justify-center bg-red-500/0 group-hover:bg-red-500/70 transition-colors">
                                                    <x-heroicon-m-x-mark class="w-3 h-3 text-white opacity-0 group-hover:opacity-100" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- DAHA FAZLA EKLE --}}
    <div class="mt-4">
        <button type="button"
                wire:click="addMoreProducts"
                class="w-full py-3 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
            + 5 Ürün Daha Ekle
        </button>
    </div>

    {{-- TASLAK KAYDET MODAL --}}
    @if($showDraftModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showDraftModal', false)">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Taslak Kaydet</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $this->filledCount }} ürün, {{ $this->totalImages }} görsel</p>
                </div>
                <div class="px-6 py-5">
                    <label class="field-label">Taslak Adı</label>
                    <input type="text"
                           wire:model="draftName"
                           wire:keydown.enter="saveDraft"
                           placeholder="Taslak adı girin..."
                           class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500"
                           autofocus>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-2">
                    <button type="button"
                            wire:click="$set('showDraftModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        İptal
                    </button>
                    <button type="button"
                            wire:click="saveDraft"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Kaydet
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- TASLAKLARI YÜKLE MODAL --}}
    @if($showLoadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showLoadModal', false)">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kayıtlı Taslaklar</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $this->drafts->count() }} taslak</p>
                    </div>
                    <button type="button" wire:click="$set('showLoadModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse($this->drafts as $draft)
                        @php
                            $draftProducts = is_array($draft->products) ? $draft->products : [];
                            $draftFilled = collect($draftProducts)->filter(fn($p) => !empty(trim($p['name'] ?? '')))->count();
                            $draftImages = collect($draftProducts)->sum(fn($p) => count($p['images'] ?? []));
                            $draftCategory = $draft->category_id ? ($this->categories[$draft->category_id] ?? null) : null;
                            $isActive = $activeDraftId === $draft->id;
                        @endphp
                        <div class="px-6 py-3 border-b border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors {{ $isActive ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1 cursor-pointer" wire:click="loadDraft({{ $draft->id }})">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $draft->name }}</span>
                                        @if($isActive)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500 text-white font-bold">AKTİF</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-gray-500">{{ $draftFilled }} ürün</span>
                                        <span class="text-xs text-gray-500">{{ $draftImages }} görsel</span>
                                        @if($draftCategory)
                                            <span class="text-xs text-amber-600 dark:text-amber-400">{{ $draftCategory }}</span>
                                        @endif
                                        <span class="text-xs text-gray-400">{{ $draft->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <button type="button"
                                        wire:click="deleteDraft({{ $draft->id }})"
                                        wire:confirm="Bu taslağı silmek istediğinize emin misiniz?"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Henüz kayıtlı taslak yok</p>
                        </div>
                    @endforelse
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 flex justify-end">
                    <button type="button"
                            wire:click="$set('showLoadModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
