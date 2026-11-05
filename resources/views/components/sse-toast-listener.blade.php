@auth
    @if(config('services.realtime.sse_enabled', true))
        {{-- Kontainer Notifikasi Melayang (Toast) Server-Sent Events --}}
        <div id="magkom-toast-container" class="magkom-toast-container toast-floating-container alerts-container" aria-live="polite"></div>

        {{-- Script Engine SSE --}}
        <script src="{{ asset('js/realtime-sse.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.MagKomRealtime) {
                    window.MagKomRealtime.init("{{ route('realtime.stream') }}");
                }
            });
        </script>
    @endif
@endauth
