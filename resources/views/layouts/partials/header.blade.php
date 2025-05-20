<header class="sticky top-0 z-30 w-full shadow-md print:hidden">
    {{-- menu --}}
    <livewire:navigation-menu />

    {{-- heading --}}
    @if (isset($heading))
        <div class="flex bg-neutral-200 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-200">
            <div class="flex-1 p-1 mx-auto" aria-label="Page Heading">
                <x-ts-icon icon="tabler.arrow-badge-right" class="inline-block size-6" alt="Arrow Icon" />
                {{ $heading }}
            </div>

            <div class="flex-1 p-1 mx-auto text-end" aria-label="Current Date">
                <time id="current-time" datetime="{{ Carbon\Carbon::today()->toDateString() }}">
                {{ Carbon\Carbon::now()->timezone(session('timezone') ?? 'UTC')->isoFormat('YYYY-MM-DD HH:mm:ss') }}
                </time>
            </div>
        </div>
    @endif
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTime() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: '2-digit', 
                day: '2-digit',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false
            };
            document.getElementById('current-time').textContent = now.toLocaleString('zh-CN', options).replace(/\//g, '-');
            setTimeout(updateTime, 1000);
        }
        updateTime();
    });
</script>