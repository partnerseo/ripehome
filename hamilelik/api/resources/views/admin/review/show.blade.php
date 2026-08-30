@extends('admin.layout')
@section('title', 'Gözden geçirme')

@section('content')
    @php
        $isWeek = $type === 'hafta';
        $heading = $isWeek ? $record->week . '. hafta' : $record->name;
    @endphp

    <div class="head">
        <div>
            <div class="eyebrow">Gözden geçirme</div>
            <h1>{{ $heading }}</h1>
            <p>Aşağıdaki metnin tıbbi doğruluğunu değerlendirin. Onayladığınızda kayıt
                uygulamaya çıkar ve altında adınız görünür.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.review.index') }}">Kuyruğa dön</a>
    </div>

    <div class="card">
        <div class="card-h">Onaylanacak içerik</div>
        <div class="card-b">
            @foreach($sections as $label => $body)
                <div class="review-section">
                    <h3>{{ $label }}</h3>
                    @if(filled($body))
                        <p>{{ $body }}</p>
                    @else
                        <p class="empty">Girilmemiş</p>
                    @endif
                </div>
            @endforeach

            <div class="review-section">
                <h3>Dayanak kaynaklar</h3>
                @if(filled($record->source_refs))
                    <ul class="sourcelist">
                        @foreach($record->source_refs as $source)
                            <li>
                                {{ $source['label'] ?? '' }}
                                @if(filled($source['url'] ?? null))
                                    — <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer">bağlantı</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="empty">Kaynak girilmemiş</p>
                @endif
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.review.approve', ['type' => $type, 'id' => $record->id]) }}">
        @csrf
        <div class="card">
            <div class="card-h">Onay</div>
            <div class="card-b grid">
                <div class="grid grid-2">
                    <div class="field">
                        <label for="reviewed_by">Onaylayan</label>
                        <input id="reviewed_by" name="reviewed_by" type="text" required
                               placeholder="Dr. Ad Soyad, Kadın Hastalıkları ve Doğum"
                               value="{{ old('reviewed_by', $record->reviewed_by ?? auth('admin')->user()->name) }}"
                               @error('reviewed_by') aria-invalid="true" @enderror>
                        @error('reviewed_by')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="reviewed_at">Onay tarihi</label>
                        <input id="reviewed_at" name="reviewed_at" type="date" required
                               value="{{ old('reviewed_at', now()->format('Y-m-d')) }}"
                               @error('reviewed_at') aria-invalid="true" @enderror>
                        @error('reviewed_at')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="review_note">Not (isteğe bağlı)</label>
                    <textarea id="review_note" name="review_note" rows="2">{{ old('review_note') }}</textarea>
                </div>

                <div class="actions" style="margin:0">
                    <button type="submit" class="btn btn-primary">Onayla ve yayına al</button>
                    <a class="btn btn-ghost"
                       href="{{ $isWeek ? route('admin.weeks.edit', $record) : route('admin.screenings.edit', $record) }}">
                        Düzeltme gerekiyor — düzenle
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection
