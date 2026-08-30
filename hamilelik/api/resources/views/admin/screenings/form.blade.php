@extends('admin.layout')
@section('title', $template->exists ? $template->name : 'Yeni tetkik')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">Tetkik</div>
            <h1>{{ $template->exists ? $template->name : 'Yeni tetkik' }}</h1>
            @if($template->exists)
                <p>@include('admin.partials.status', ['status' => $template->status])</p>
            @endif
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.screenings.index') }}">Listeye dön</a>
    </div>

    <form method="POST"
          action="{{ $template->exists ? route('admin.screenings.update', $template) : route('admin.screenings.store') }}">
        @csrf
        @if($template->exists) @method('PUT') @endif

        <div class="card">
            <div class="card-h">Tetkik</div>
            <div class="card-b grid">
                <div class="grid grid-2">
                    <div class="field">
                        <label for="code">Kod</label>
                        <input id="code" name="code" type="text" value="{{ old('code', $template->code) }}"
                               required @error('code') aria-invalid="true" @enderror>
                        <span class="hint">Kalıcı kimlik; randevu üretimi buna bağlı, sonradan değiştirmeyin.</span>
                        @error('code')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="name">Ad</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $template->name) }}"
                               required @error('name') aria-invalid="true" @enderror>
                        @error('name')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="category">Kategori</label>
                        <select id="category" name="category">
                            @foreach(['usg' => 'Ultrason', 'lab' => 'Laboratuvar', 'vaccine' => 'Aşı', 'visit' => 'Muayene'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $template->category) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="week_start">Başlangıç haftası</label>
                        <input id="week_start" name="week_start" type="number" min="1" max="42"
                               value="{{ old('week_start', $template->week_start) }}" required
                               @error('week_start') aria-invalid="true" @enderror>
                        @error('week_start')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="week_end">Bitiş haftası</label>
                        <input id="week_end" name="week_end" type="number" min="1" max="42"
                               value="{{ old('week_end', $template->week_end) }}" required
                               @error('week_end') aria-invalid="true" @enderror>
                        @error('week_end')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="sort">Sıra</label>
                        <input id="sort" name="sort" type="number" min="0" max="999"
                               value="{{ old('sort', $template->sort ?? 0) }}">
                    </div>
                    <div class="field">
                        <label for="locale">Dil</label>
                        <select id="locale" name="locale">
                            <option value="tr" @selected(old('locale', $template->locale) === 'tr')>Türkçe</option>
                            <option value="en" @selected(old('locale', $template->locale) === 'en')>English</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="country">Ülke</label>
                        <select id="country" name="country">
                            <option value="TR" @selected(old('country', $template->country) === 'TR')>Türkiye</option>
                        </select>
                    </div>
                </div>

                <label class="check">
                    <input type="checkbox" name="is_optional" value="1"
                           @checked(old('is_optional', $template->is_optional))>
                    Tercihe bağlı
                </label>
            </div>
        </div>

        @include('admin.partials.review-fields', ['record' => $template])

        <div class="actions">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a class="btn btn-ghost" href="{{ route('admin.screenings.index') }}">Vazgeç</a>
        </div>
    </form>
@endsection
