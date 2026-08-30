@extends('admin.layout')
@section('title', 'Hafta içerikleri')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">İçerik</div>
            <h1>Hafta içerikleri</h1>
            <p>Metinler özgün yazılmalıdır; başka bir kaynaktan kopyalanan cümle telif ihlalidir.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.weeks.create') }}">Yeni hafta</a>
    </div>

    <div class="card">
        <div class="card-b" style="border-bottom:1px solid var(--line)">
            <form method="GET" class="grid grid-3" style="gap:10px">
                <div class="field">
                    <label for="durum">Durum</label>
                    <select id="durum" name="durum" onchange="this.form.submit()">
                        <option value="">Hepsi</option>
                        <option value="draft" @selected($status === 'draft')>Taslak</option>
                        <option value="in_review" @selected($status === 'in_review')>Gözden geçiriliyor</option>
                        <option value="published" @selected($status === 'published')>Yayında</option>
                    </select>
                </div>
                <div class="field">
                    <label for="dil">Dil</label>
                    <select id="dil" name="dil" onchange="this.form.submit()">
                        <option value="">Hepsi</option>
                        <option value="tr" @selected($locale === 'tr')>Türkçe</option>
                        <option value="en" @selected($locale === 'en')>English</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="tablewrap">
            <table>
                <thead>
                <tr>
                    <th>Hafta</th>
                    <th>Dil</th>
                    <th>Boyut</th>
                    <th>Durum</th>
                    <th>Gözden geçiren</th>
                    <th>Onay tarihi</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($contents as $content)
                    <tr>
                        <td class="num"><strong>{{ $content->week }}</strong></td>
                        <td>{{ $content->locale }}</td>
                        <td>{{ $content->baby_size_label ?: '—' }}</td>
                        <td>@include('admin.partials.status', ['status' => $content->status])</td>
                        <td>{{ $content->reviewed_by ?: '—' }}</td>
                        <td class="num">{{ $content->reviewed_at?->format('d.m.Y') ?: '—' }}</td>
                        <td class="min">
                            <a class="btn btn-ghost btn-sm" href="{{ route('admin.weeks.edit', $content) }}">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state">Henüz hafta içeriği yok.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($contents->hasPages())
        <div style="margin-top:16px">{{ $contents->links() }}</div>
    @endif
@endsection
