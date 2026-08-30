<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · Hamilelik</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="shell">
    <aside class="side">
        <div class="brand">
            <span>İçerik paneli</span>
            <b>Hamilelik</b>
        </div>

        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Genel bakış</a>
            <a href="{{ route('admin.review.index') }}" @if(request()->routeIs('admin.review.*')) aria-current="page" @endif>
                Onay kuyruğu
                @if($pendingReview > 0)<span class="count">{{ $pendingReview }}</span>@endif
            </a>
            <a href="{{ route('admin.weeks.index') }}" @if(request()->routeIs('admin.weeks.*')) aria-current="page" @endif>Hafta içerikleri</a>
            <a href="{{ route('admin.screenings.index') }}" @if(request()->routeIs('admin.screenings.*')) aria-current="page" @endif>Tetkik takvimi</a>
        </nav>

        <div class="side-foot">
            <div class="who">
                <span class="avatar">{{ auth('admin')->user()->initials() }}</span>
                <span>{{ auth('admin')->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" style="width:100%">Çıkış yap</button>
            </form>
        </div>
    </aside>

    <main class="main">
        @if(session('ok'))<div class="flash flash-ok">{{ session('ok') }}</div>@endif
        @if(session('warn'))<div class="flash flash-warn">{{ session('warn') }}</div>@endif
        @if($errors->any() && ! isset($hideErrorSummary))
            <div class="flash flash-err">Kaydedilemedi. Aşağıdaki alanları kontrol edin.</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
