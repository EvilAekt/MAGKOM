@extends('errors.layout')

@section('title', '500 - Terjadi Kesalahan Server')
@section('code', '500')
@section('code_color', '#DC2626')
@section('icon_bg', '#FEE2E2')
@section('icon_color', '#991B1B')

@section('icon')
<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
</svg>
@endsection

@section('headline', 'Terjadi Kesalahan pada Server')
@section('message', 'Mohon maaf, sistem sedang mengalami kendala teknis saat memproses permintaan Anda. Tim teknis Dinas Kominfo Sleman sedang menangani hal ini. Silakan coba kembali beberapa saat lagi.')
@section('button_url', url('/'))
@section('button_text', 'Kembali ke Beranda')
