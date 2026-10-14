@auth
    {{-- Kontainer Notifikasi Melayang (Toast) Server-Sent Events --}}
    <div id="magkom-toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4" aria-live="polite"></div>

    {{-- Script Engine SSE --}}
    <script src="{{ asset('js/realtime-sse.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.MagKomRealtime) {
                window.MagKomRealtime.init("{{ route('realtime.stream') }}");
            }
        });
    </script>
@endauth
