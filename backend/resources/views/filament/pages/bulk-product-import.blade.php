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

    {{-- HEADER BAR --}}
    <div class="rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 p-5 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 block">Kategori</label>
                <select wire:model.live="categoryId"
                        class="w-full rounded-lg border-0 bg-white/10 text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 px-4 py-2.5 text-sm backdrop-blur">
                    <option value="" class="text-gray-900">Kategori seçin...</option>
                    @foreach($this->categories as $id => $name)
                        <option value="{{ $id }}" class="text-gray-900">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 bg-white/5 rounded-lg px-4 py-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="autoPublish" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
                <span class="text-sm font-medium text-gray-300">Otomatik Yayınla</span>
            </div>
        </div>
    </div>

    {{-- PRODUCT CARDS --}}
    <div class="space-y-3">
        @foreach($products as $index => $product)
            @php
                $hasData = !empty(trim($product['name'] ?? ''));
                $imageCount = count($product['images'] ?? []);
                $hasSku = !empty(trim($product['sku'] ?? ''));
                $isReady = $hasData && $imageCount > 0;
            @endphp
            <div class="bulk-card {{ $hasData ? 'has-data' : '' }} rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden"
                 x-data="{ open: {{ $index < 3 ? 'true' : 'false' }} }">

                {{-- ACCORDION HEADER --}}
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        {{-- Number badge --}}
                        <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $hasData ? 'bg-amber-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                            {{ $index + 1 }}
                        </span>

                        {{-- Product info --}}
                        <span class="font-medium text-sm truncate {{ $hasData ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $hasData ? $product['name'] : 'Boş ürün kartı' }}
                        </span>

                        @if($imageCount > 0)
                            <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                <x-heroicon-m-photo class="w-3 h-3" />
                                {{ $imageCount }}
                            </span>
                        @endif

                        @if($hasSku)
                            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-mono">
                                {{ $product['sku'] }}
                            </span>
                        @endif

                        @if($isReady)
                            <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                <x-heroicon-m-check-circle class="w-3 h-3" />
                                Hazır
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        {{-- Delete button --}}
                        <span
                            wire:click.stop="removeProduct({{ $index }})"
                            wire:confirm="Bu kartı silmek istediğinize emin misiniz?"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer">
                            <x-heroicon-m-trash class="w-4 h-4" />
                        </span>

                        {{-- Chevron --}}
                        <x-heroicon-m-chevron-down class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                   ::class="open ? 'rotate-180' : ''" />
                    </div>
                </button>

                {{-- ACCORDION CONTENT --}}
                <div x-show="open" x-cloak>
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                            {{-- LEFT: Form Fields --}}
                            <div class="lg:col-span-7 space-y-4">
                                {{-- Product Name --}}
                                <div>
                                    <label class="field-label">Ürün Adı <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           wire:model.blur="products.{{ $index }}.name"
                                           placeholder="Örn: Premium Pamuk Nevresim Takımı"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-amber-500 focus:ring-amber-500 dark:text-white">
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label class="field-label">Kısa Açıklama</label>
                                    <textarea wire:model.blur="products.{{ $index }}.description"
                                              rows="2"
                                              placeholder="Ürün hakkında kısa bilgi..."
                                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-amber-500 focus:ring-amber-500 dark:text-white resize-none"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    {{-- SKU --}}
                                    <div>
                                        <label class="field-label">Ürün Kodu (SKU)</label>
                                        <input type="text"
                                               wire:model.blur="products.{{ $index }}.sku"
                                               placeholder="Örn: NVR-001"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-amber-500 focus:ring-amber-500 dark:text-white font-mono">
                                    </div>

                                    {{-- Featured Toggle --}}
                                    <div class="flex items-end pb-1">
                                        <label class="relative inline-flex items-center cursor-pointer gap-3">
                                            <input type="checkbox"
                                                   wire:model.live="products.{{ $index }}.featured"
                                                   class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Öne Çıkan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: Image Upload --}}
                            <div class="lg:col-span-5">
                                <label class="field-label">Görseller</label>

                                {{-- Upload Area --}}
                                <label class="relative flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-xl cursor-pointer transition-colors
                                    {{ !empty($uploading[$index] ?? false) ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/10' : 'border-gray-300 dark:border-gray-600 hover:border-amber-400 hover:bg-amber-50/50 dark:hover:bg-amber-900/10' }}">

                                    @if(!empty($uploading[$index] ?? false))
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Yükleniyor...</span>
                                        </div>
                                    @else
                                        <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-gray-400 mb-1" />
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Sürükle & Bırak veya Tıkla</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">JPG, PNG, WebP</span>
                                    @endif

                                    <input type="file"
                                           wire:model="fileUploads.{{ $index }}"
                                           multiple
                                           accept="image/*"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </label>

                                {{-- Image Thumbnails --}}
                                @if(!empty($product['images']))
                                    <div class="flex flex-wrap gap-1.5 mt-2 max-h-24 overflow-y-auto">
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

    {{-- BOTTOM BAR --}}
    <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 sm:px-6 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <button type="button"
                    wire:click="addMoreProducts"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-600 dark:text-gray-400 hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                <x-heroicon-m-plus class="w-4 h-4" />
                5 Ürün Daha Ekle
            </button>

            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold">
                        {{ $this->filledCount }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400">ürün</span>
                </div>
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold">
                        {{ $this->totalImages }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400">görsel</span>
                </div>
            </div>

            <button type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="save">
                    <x-heroicon-m-arrow-up-tray class="w-4 h-4" />
                </span>
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Tümünü Kaydet</span>
                <span wire:loading wire:target="save">Kaydediliyor...</span>
            </button>
        </div>
    </div>

    {{-- HOW IT WORKS --}}
    <div class="mt-6 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-base mb-3 flex items-center gap-2 text-gray-800 dark:text-gray-200">
            <x-heroicon-o-information-circle class="w-5 h-5 text-amber-500" />
            Nasıl Çalışır?
        </h3>
        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
            <li class="flex items-start gap-2">
                <span class="text-amber-500 font-bold mt-0.5">1.</span>
                <span>Üstteki dropdown'dan <strong>kategori</strong> seçin.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-amber-500 font-bold mt-0.5">2.</span>
                <span>Her karta <strong>ürün adı</strong> girin. Boş kartlar atlanır.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-amber-500 font-bold mt-0.5">3.</span>
                <span>Her ürüne <strong>görsel</strong> yükleyin.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-amber-500 font-bold mt-0.5">4.</span>
                <span><strong>"Tümünü Kaydet"</strong> butonuna tıklayın.</span>
            </li>
        </ul>
    </div>
</x-filament-panels::page>
