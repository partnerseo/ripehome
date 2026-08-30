<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş · Hamilelik</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="login">
    <div class="card">
        <div class="card-b">
            <div class="brand">
                <span>İçerik paneli</span>
                <b>Hamilelik</b>
            </div>

            <form method="POST" action="{{ route('admin.login') }}" class="grid" style="margin-top:20px">
                @csrf

                <div class="field">
                    <label for="email">E-posta</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           autocomplete="username" required autofocus
                           @error('email') aria-invalid="true" @enderror>
                    @error('email')<span class="err">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="password">Parola</label>
                    <input id="password" name="password" type="password"
                           autocomplete="current-password" required
                           @error('password') aria-invalid="true" @enderror>
                    @error('password')<span class="err">{{ $message }}</span>@enderror
                </div>

                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    Bu cihazda oturumu açık tut
                </label>

                <button type="submit" class="btn btn-primary">Giriş yap</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
