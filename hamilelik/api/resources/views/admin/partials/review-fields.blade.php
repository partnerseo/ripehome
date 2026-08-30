{{-- Onay alanları. Model de aynı kuralı zorlar; buradaki doğrulama
     kullanıcıya hata sayfası yerine alanın yanında mesaj gösterir. --}}
<div class="card">
    <div class="card-h">Tıbbi gözden geçirme</div>
    <div class="card-b grid">
        <p style="margin:0;font-size:13px;color:var(--muted)">
            Gözden geçiren kişi ve tarih girilmeden içerik yayına alınamaz.
            Yayındaki bir metni değiştirirseniz önceki onay o metni kapsamaz;
            kayıt otomatik olarak taslağa döner.
        </p>

        <div class="grid grid-3">
            <div class="field">
                <label for="status">Durum</label>
                <select id="status" name="status" @error('status') aria-invalid="true" @enderror>
                    <option value="draft" @selected(old('status', $record->status) === 'draft')>Taslak</option>
                    <option value="in_review" @selected(old('status', $record->status) === 'in_review')>Gözden geçiriliyor</option>
                    <option value="published" @selected(old('status', $record->status) === 'published')>Yayında</option>
                </select>
                @error('status')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="reviewed_by">Gözden geçiren</label>
                <input id="reviewed_by" name="reviewed_by" type="text"
                       placeholder="Dr. Ad Soyad, Kadın Hastalıkları ve Doğum"
                       value="{{ old('reviewed_by', $record->reviewed_by) }}"
                       @error('reviewed_by') aria-invalid="true" @enderror>
                @error('reviewed_by')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="reviewed_at">Onay tarihi</label>
                <input id="reviewed_at" name="reviewed_at" type="date"
                       value="{{ old('reviewed_at', $record->reviewed_at?->format('Y-m-d')) }}"
                       @error('reviewed_at') aria-invalid="true" @enderror>
                @error('reviewed_at')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="field">
            <label for="review_note">Gözden geçirme notu</label>
            <textarea id="review_note" name="review_note" rows="2">{{ old('review_note', $record->review_note) }}</textarea>
        </div>

        <div class="field">
            <label>Dayanak kaynaklar</label>
            <span class="hint">Her iddianın arkasında bir kaynak olmalı. Bağlantı zorunlu değil.</span>
            <div class="sources" id="sources">
                @php $rows = old('source_refs', $record->source_refs ?? []); @endphp
                @foreach(array_pad($rows, max(1, count($rows)), ['label' => '', 'url' => '']) as $i => $row)
                    <div class="source-row">
                        <input type="text" name="source_refs[{{ $i }}][label]" placeholder="Kaynak adı"
                               value="{{ $row['label'] ?? '' }}">
                        <input type="url" name="source_refs[{{ $i }}][url]" placeholder="https://…"
                               value="{{ $row['url'] ?? '' }}">
                        <button type="button" class="btn btn-ghost btn-sm" data-remove-source>Sil</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ghost btn-sm" id="add-source" style="align-self:flex-start">
                Kaynak ekle
            </button>
        </div>
    </div>
</div>

<script>
    // Kaynak satırları: küçük bir iş için kütüphane getirmeye değmez.
    (function () {
        const list = document.getElementById('sources');
        const add = document.getElementById('add-source');
        if (!list || !add) return;

        const nextIndex = () => list.querySelectorAll('.source-row').length;

        add.addEventListener('click', () => {
            const i = nextIndex();
            const row = document.createElement('div');
            row.className = 'source-row';
            row.innerHTML =
                '<input type="text" name="source_refs[' + i + '][label]" placeholder="Kaynak adı">' +
                '<input type="url" name="source_refs[' + i + '][url]" placeholder="https://…">' +
                '<button type="button" class="btn btn-ghost btn-sm" data-remove-source>Sil</button>';
            list.appendChild(row);
            row.querySelector('input').focus();
        });

        list.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-source]');
            if (!button) return;
            const rows = list.querySelectorAll('.source-row');
            // Son satır silinmez, boşaltılır: form hep en az bir satır gösterir.
            if (rows.length === 1) {
                rows[0].querySelectorAll('input').forEach((input) => { input.value = ''; });
                return;
            }
            button.closest('.source-row').remove();
        });
    })();
</script>
