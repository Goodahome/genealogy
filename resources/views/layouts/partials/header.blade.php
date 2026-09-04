<header class="sticky top-0 z-30 w-full print:hidden">
    {{-- menu --}}
    <livewire:navigation-menu />

    {{-- heading --}}
    @if (isset($heading))
        <div class="zp-paper-bar flex text-[var(--zp-ink-soft)]">
            <div class="zp-display flex-1 p-1 mx-auto" aria-label="Page Heading">
                <x-ts-icon icon="tabler.arrow-badge-right" class="inline-block size-6 text-[var(--zp-seal)]" alt="Arrow Icon" />
                {{ $heading }}
            </div>

            <div class="flex-1 p-1 mx-auto text-end text-sm sm:text-base" aria-label="Current Date">
                <time id="current-time" datetime="{{ Carbon\Carbon::today()->toDateString() }}">
                    {{ Carbon\Carbon::now()->timezone(session('timezone') ?? 'UTC')->isoFormat('YYYY-MM-DD HH:mm:ss') }}
                </time>
                <span class="mx-1 text-[var(--zp-gold-deep)]">·</span>
                <span id="current-lunar" class="whitespace-nowrap"></span>
            </div>
        </div>
    @endif
</header>

<script src="{{ asset('js/lunar.js') }}"></script>
<script src="{{ asset('js/chinese-lunar.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeEl = document.getElementById('current-time');
        const lunarEl = document.getElementById('current-lunar');
        if (!timeEl || !lunarEl || !window.ChineseLunar) {
            return;
        }

        let lastDateKey = '';

        function updateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            };

            timeEl.textContent = now.toLocaleString('zh-CN', options).replace(/\//g, '-');
            timeEl.setAttribute(
                'datetime',
                now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0')
            );

            const dateKey = timeEl.getAttribute('datetime');
            if (dateKey !== lastDateKey) {
                lunarEl.textContent = window.ChineseLunar.format(now);
                lastDateKey = dateKey;
            }

            setTimeout(updateTime, 1000);
        }

        updateTime();
    });
</script>
