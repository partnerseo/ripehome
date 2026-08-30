@php
    $map = [
        'published' => ['badge-ok', 'Yayında'],
        'in_review' => ['badge-warn', 'Gözden geçiriliyor'],
    ];
    [$class, $label] = $map[$status] ?? ['badge-draft', 'Taslak'];
@endphp
<span class="badge {{ $class }}">{{ $label }}</span>
