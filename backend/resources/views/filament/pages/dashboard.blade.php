<x-filament-panels::page>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-500/10">
                    <x-heroicon-o-shopping-bag class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Ürün</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalProducts) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/products" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">Tümünü Gör &rarr;</a>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <x-heroicon-o-folder class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategoriler</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCategories) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/categories" class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:underline">Tümünü Gör &rarr;</a>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-500/10">
                    <x-heroicon-o-envelope class="h-6 w-6 text-sky-600 dark:text-sky-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mesajlar</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessages) }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <a href="/admin/contact-messages" class="text-sm font-medium text-sky-600 dark:text-sky-400 hover:underline">Tümünü Gör &rarr;</a>
                @if($unreadMessages > 0)
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-500/10 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">{{ $unreadMessages }} okunmamış</span>
                @endif
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/10">
                    <x-heroicon-o-shopping-cart class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Siparişler</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalOrders) }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <a href="/admin/wholesale-orders" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">Tümünü Gör &rarr;</a>
                @if($pendingOrders > 0)
                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">{{ $pendingOrders }} bekliyor</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Two Column --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-shopping-cart class="h-5 w-5 text-amber-500" /> Son Siparişler
                    </h3>
                    <a href="/admin/wholesale-orders" class="text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400">Tümü &rarr;</a>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($recentOrders as $order)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->company_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->contact_person }} &middot; {{ $order->created_at->format('d.m.Y') }}</p>
                        </div>
                        <span @class([
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-400' => $order->status === 'pending',
                            'bg-blue-100 text-blue-800 dark:bg-blue-500/10 dark:text-blue-400' => $order->status === 'reviewing',
                            'bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400' => in_array($order->status, ['approved', 'completed']),
                            'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400' => $order->status === 'rejected',
                        ])>{{ match($order->status) { 'pending' => 'Yeni', 'reviewing' => 'İnceleniyor', 'approved' => 'Onaylı', 'rejected' => 'Reddedildi', 'completed' => 'Tamamlandı', default => $order->status } }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <x-heroicon-o-inbox class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Henüz sipariş yok</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-envelope class="h-5 w-5 text-sky-500" /> Son Mesajlar
                    </h3>
                    <a href="/admin/contact-messages" class="text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400">Tümü &rarr;</a>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($recentMessages as $msg)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $msg->name }}</p>
                                @if(!$msg->is_read)
                                    <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $msg->subject ?: Str::limit($msg->message, 50) }}</p>
                        </div>
                        <span class="ml-3 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $msg->created_at->format('d.m') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <x-heroicon-o-inbox class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Henüz mesaj yok</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Categories --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-chart-bar class="h-5 w-5 text-primary-500" /> En Çok Ürün İçeren Kategoriler
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @foreach($topCategories as $cat)
                @php $max = $topCategories->max('products_count') ?: 1; $pct = round(($cat->products_count / $max) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cat->name }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $cat->products_count }} ürün</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5">
                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Login Logs --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-finger-print class="h-5 w-5 text-violet-500" /> Son Giriş Kayıtları
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Adresi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Konum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarayıcı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($loginLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300 font-mono">{{ $log->ip_address }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <x-heroicon-o-map-pin class="h-3.5 w-3.5" />
                                    <span>{{ $log->city && $log->country ? $log->city . ', ' . $log->country : ($log->country ?? '-') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $ua = $log->user_agent ?? '';
                                    if (str_contains($ua, 'Edg')) $browser = 'Edge';
                                    elseif (str_contains($ua, 'Chrome')) $browser = 'Chrome';
                                    elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
                                    elseif (str_contains($ua, 'Safari')) $browser = 'Safari';
                                    else $browser = Str::limit($ua, 20);

                                    $color = match($browser) {
                                        'Chrome' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'Firefox' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                        'Safari' => 'bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                                        'Edge' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">{{ $browser }}</span>
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" title="{{ $log->logged_in_at?->format('d.m.Y H:i:s') }}">
                                {{ $log->logged_in_at?->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <x-heroicon-o-finger-print class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Henüz giriş kaydı yok</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
