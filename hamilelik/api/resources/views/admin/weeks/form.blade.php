@extends('admin.layout')
@section('title', $content->exists ? $content->week . '. hafta' : 'Yeni hafta')

@section('content')
    <div class="head">
        <div>
            <div class="eyebrow">Hafta içeriği</div>
            <h1>{{ $content->exists ? $content->week . '. hafta' : 'Yeni hafta içeriği' }}</h1>
            @if($content->exists)
                <p>@include('admin.partials.status', ['status' => $content->status])</p>
            @endif
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.weeks.index') }}">Listeye dön</a>
    </div>

    <form method="POST"
          action="{{ $content->exists ? route('admin.weeks.update', $content) : route('admin.weeks.store') }}">
        @csrf
        @if($content->exists) @method('PUT') @endif

        <div class="card">
            <div class="card-h">Hafta</div>
            <div class="card-b grid grid-3">
                <div class="field">
                    <label for="week">Hafta</label>
                    <input id="week" name="week" type="number" min="1" max="42"
                           value="{{ old('week', $content->week) }}" required
                           @error('week') aria-invalid="true" @enderror>
                    @error('week')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label for="locale">Dil</label>
                    <select id="locale" name="locale">
                        <option value="tr" @selected(old('locale', $content->locale) === 'tr')>Türkçe</option>
                        <option value="en" @selected(old('locale', $content->locale) === 'en')>English</option>
                    </select>
                </div>
                <div class="field">
                    <label for="baby_size_label">Boyut karşılaştırması</label>
                    <input id="baby_size_label" name="baby_size_label" type="text" placeholder="mısır koçanı"
                           value="{{ old('baby_size_label', $content->baby_size_label) }}">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-h">İçerik</div>
            <div class="card-b grid">
                <div class="grid grid-2">
                    <div class="field">
                        <label for="baby_length_mm">Boy (mm)</label>
                        <input id="baby_length_mm" name="baby_length_mm" type="number" min="0" max="600"
                               value="{{ old('baby_length_mm', $content->baby_length_mm) }}">
                        @error('baby_length_mm')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="baby_weight_g">Ağırlık (g)</label>
                        <input id="baby_weight_g" name="baby_weight_g" type="number" min="0" max="6000"
                               value="{{ old('baby_weight_g', $content->baby_weight_g) }}">
                        @error('baby_weight_g')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>

                @foreach(['baby_body' => 'Bebekte neler oluyor', 'mother_body' => 'Annede neler oluyor', 'tips_body' => 'Bu hafta ipuçları'] as $name => $label)
                    <div class="field">
                        <label for="{{ $name }}">{{ $label }}</label>
                        <textarea id="{{ $name }}" name="{{ $name }}" rows="4">{{ old($name, $content->{$name}) }}</textarea>
                        @error($name)<span class="err">{{ $message }}</span>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        @include('admin.partials.review-fields', ['record' => $content])

        <div class="actions">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a class="btn btn-ghost" href="{{ route('admin.weeks.index') }}">Vazgeç</a>
        </div>
    </form>

    @if($content->exists)
        <form method="POST" action="{{ route('admin.weeks.destroy', $content) }}" style="margin-top:26px"
              onsubmit="return confirm('Bu hafta içeriği silinsin mi? Geri alınamaz.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Bu içeriği sil</button>
        </form>
    @endif
@endsection
