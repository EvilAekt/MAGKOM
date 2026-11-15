@auth
    @if(config('services.realtime.sse_enabled', true))
        {{-- Kontainer Notifikasi Melayang (Toast) Server-Sent Events --}}
        <div id="magkom-toast-container" class="magkom-toast-container toast-floating-container alerts-container" data-sse-url="{{ route('realtime.stream') }}" aria-live="polite"></div>

        {{-- Script Engine SSE --}}
        <script src="{{ asset('js/realtime-sse.js') }}"></script>
    @endif
@endauth
