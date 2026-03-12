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

    {{-- ÜST KONTROL ALANI --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            {{-- Kategori --}}
            <div class="md:col-span-2">
                <label for="catSelect" class="field-label">Kategori</label>
                <select id="catSelect"
                        wire:model.live="categoryId"
                        class="fi-select-input w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-amber-500 focus:ring-amber-500 py-2">
                    <option value="">-- Kategori seçin --</option>
                    @foreach($this->categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Otomatik Yayınla --}}
            <div>
                <label class="field-label">Durum</label>
                <label class="inline-flex items-center cursor-pointer gap-2 py-2">
                    <input type="checkbox" wire:model.live="autoPublish" class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:bg-amber-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all relative"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Otomatik Yayınla</span>
                </label>
            </div>

            {{-- Kaydet Butonu --}}
            <div>
                <button type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all disabled:opacity-50">
                    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">Tümünü Kaydet ({{ $this->filledCount }} ürün)</span>
                    <span wire:loading wire:target="save">Kaydediliyor...</span>
                </button>
            </div>
        </div>
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
</x-filament-panels::page>
