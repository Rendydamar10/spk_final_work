@props(['coin', 'size' => 'w-8 h-8'])

@if($coin?->logo_url)
    <img
        src="{{ $coin->logo_url }}"
        alt="Logo {{ $coin->name }}"
        loading="lazy"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"
        {{ $attributes->merge(['class' => $size.' rounded-full object-cover shrink-0']) }}
    >
    <span
        aria-hidden="true"
        style="display: none;"
        {{ $attributes->merge(['class' => $size.' rounded-full bg-slate-100 text-slate-500 items-center justify-center font-bold text-xs shrink-0']) }}
    >
        {{ strtoupper(substr($coin?->symbol ?? '?', 0, 2)) }}
    </span>
@else
    <span
        aria-hidden="true"
        {{ $attributes->merge(['class' => $size.' rounded-full bg-slate-100 text-slate-500 inline-flex items-center justify-center font-bold text-xs shrink-0']) }}
    >
        {{ strtoupper(substr($coin?->symbol ?? '?', 0, 2)) }}
    </span>
@endif
