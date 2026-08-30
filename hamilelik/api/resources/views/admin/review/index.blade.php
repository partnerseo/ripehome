@extends('admin.layout')
@section('title', 'Onay kuyruğu')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">Gözden geçirme</div>
            <h1>Onay kuyruğu</h1>
            <p>Onay bekleyen içerikler. Buradan okuyup onayladığınız kayıtlar uygulamaya çıkar;
                onaylamadıklarınız kullanıcıya hiç ulaşmaz.</p>
        </div>
    </div>

    @if($weeks->isEmpty() && $screenings->isEmpty())
        <div class="card"><div class="empty-state">Onay bekleyen içerik yok. Hepsi yayında.</div></div>
    @endif

    @if($screenings->isNotEmpty())
        <div class="card">
            <div class="card-h">Tetkikler ({{ $screenings->count() }})</div>
            <div class="tablewrap">
                <table>
                    <tbody>
                    @foreach($screenings as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->name }}</strong><br>
                                <span style="color:var(--muted);font-size:13px">
                                    {{ $template->week_start === $template->week_end
                                        ? $template->week_start . '. hafta'
                                        : $template->week_start . '–' . $template->week_end . '. hafta' }}
                                </span>
                            </td>
                            <td>@include('admin.partials.status', ['status' => $template->status])</td>
                            <td class="min">
                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('admin.review.show', ['type' => 'tetkik', 'id' => $template->id]) }}">
                                    Oku ve onayla
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($weeks->isNotEmpty())
        <div class="card">
            <div class="card-h">Hafta içerikleri ({{ $weeks->count() }})</div>
            <div class="tablewrap">
                <table>
                    <tbody>
                    @foreach($weeks as $content)
                        <tr>
                            <td>
                                <strong>{{ $content->week }}. hafta</strong>
                                <span style="color:var(--faint);font-size:13px"> · {{ $content->locale }}</span><br>
                                <span style="color:var(--muted);font-size:13px">
                                    {{ \Illuminate\Support\Str::limit($content->baby_body, 70) ?: 'Metin girilmemiş' }}
                                </span>
                            </td>
                            <td>@include('admin.partials.status', ['status' => $content->status])</td>
                            <td class="min">
                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('admin.review.show', ['type' => 'hafta', 'id' => $content->id]) }}">
                                    Oku ve onayla
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
