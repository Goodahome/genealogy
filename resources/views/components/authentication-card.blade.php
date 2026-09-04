@props(['logo' => null, 'header' => null])

<div class="zp-auth-wrap">
    @if ($logo)
        <div class="mb-3 shrink-0">
            {{ $logo }}
        </div>
    @endif

    {{-- 高度随表单内容，不被拉长；侧图绝对定位跟随表单高度 --}}
    <div class="zp-auth-frame">
        <div class="zp-auth-media" aria-hidden="true">
            <img src="{{ asset('img/genealogy-research.webp') }}"
                 alt=""
                 class="h-full w-full object-cover object-center">
        </div>

        <div class="zp-auth-panel">
            @if ($header)
                <h1 class="zp-display mb-6 text-4xl font-bold text-[var(--zp-ink)]">{{ $header }}</h1>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
