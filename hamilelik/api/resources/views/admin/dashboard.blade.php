@extends('admin.layout')
@section('title', 'Genel bakış')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">Genel bakış</div>
            <h1>Neyin eksik olduğu</h1>
            <p>İçerik ve tetkikler hekim onayı olmadan uygulamaya çıkmaz. Aşağıdaki sayılar
                kullanıcıya ulaşan ve ulaşmayan kayıtları gösterir.</p>
        </div>
    </div>

    <div class="stats">
        <div class="stat">
            <b>{{ $weekPublished }}<span style="font-size:15px;color:var(--faint)"> / 42</span></b>
            <span>hafta yayında</span>
        </div>
        <div class="stat @if($weekPending > 0) attention @endif">
            <b>{{ $weekPending }}</b>
            <span>hafta onay bekliyor</span>
        </div>
        <div class="stat @if($weekMissing > 0) attention @endif">
            <b>{{ $weekMissing }}</b>
            <span>hafta hiç yazılmadı</span>
        </div>
        <div class="stat">
            <b>{{ $screeningPublished }}</b>
            <span>tetkik yayında</span>
        </div>
        <div class="stat @if($screeningPending > 0) attention @endif">
            <b>{{ $screeningPending }}</b>
            <span>tetkik onay bekliyor</span>
        </div>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="card-b">
            <p style="margin:0 0 14px;color:var(--muted);font-size:14px">
                Hekim onayı olmayan hiçbir metin uygulamaya gitmiyor. Yayındaki bir metni
                değiştirmek de onayı geçersiz kılıyor ve kaydı taslağa döndürüyor.
            </p>
            <div class="actions" style="margin:0">
                <a class="btn btn-primary" href="{{ route('admin.review.index') }}">Onay kuyruğuna git</a>
                <a class="btn btn-ghost" href="{{ route('admin.weeks.create') }}">Yeni hafta içeriği</a>
            </div>
        </div>
    </div>
@endsection
