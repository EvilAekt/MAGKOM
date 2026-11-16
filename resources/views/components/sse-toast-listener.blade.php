@auth
    {{-- Kontainer Notifikasi Melayang (Toast) Realtime SSE / Polling Fallback (PERF-001) --}}
    <div id="magkom-toast-container"
         class="magkom-toast-container toast-floating-container alerts-container"
         data-sse-enabled="{{ config('services.realtime.sse_enabled', false) ? 'true' : 'false' }}"
         data-sse-url="{{ route('realtime.stream') }}"
         data-poll-url="{{ route('realtime.poll') }}"
         data-poll-interval="{{ (int) config('services.realtime.polling_interval_seconds', 15) * 1000 }}"
         aria-live="polite"></div>

    {{-- Script Engine Realtime --}}
    <script src="{{ asset('js/realtime-sse.js') }}"></script>
@endauth
