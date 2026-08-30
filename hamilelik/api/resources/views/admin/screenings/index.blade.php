@extends('admin.layout')
@section('title', 'Tetkik takvimi')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">Takvim</div>
            <h1>Tetkik takvimi</h1>
            <p>Yanlış bir hafta, kaçırılmış bir tarama demektir. Onaysız tetkikten randevu üretilmez.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.screenings.create') }}">Yeni tetkik</a>
    </div>

    <div class="card">
        <div class="tablewrap">
            <table>
                <thead>
                <tr>
                    <th>Tetkik</th>
                    <th>Kategori</th>
                    <th>Hafta</th>
                    <th>Durum</th>
                    <th>Gözden geçiren</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php $labels = ['usg' => 'Ultrason', 'lab' => 'Laboratuvar', 'vaccine' => 'Aşı', 'visit' => 'Muayene']; @endphp
                @forelse($templates as $template)
                    <tr>
                        <td>
                            <strong>{{ $template->name }}</strong>
                            @if($template->is_optional)
                                <span style="color:var(--faint);font-size:12.5px"> · tercihe bağlı</span>
                            @endif
                        </td>
                        <td>{{ $labels[$template->category] ?? $template->category }}</td>
                        <td class="num">
                            {{ $template->week_start === $template->week_end
                                ? $template->week_start
                                : $template->week_start . '–' . $template->week_end }}
                        </td>
                        <td>@include('admin.partials.status', ['status' => $template->status])</td>
                        <td>{{ $template->reviewed_by ?: '—' }}</td>
                        <td class="min">
                            <a class="btn btn-ghost btn-sm" href="{{ route('admin.screenings.edit', $template) }}">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state">Henüz tetkik yok.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
