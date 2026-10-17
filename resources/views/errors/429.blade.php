@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('code_color', '#DC2626')
@section('icon_bg', '#FEE2E2')
@section('icon_color', '#991B1B')

@section('icon')
<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
</svg>
@endsection

@section('headline', 'Terlalu Banyak Permintaan')
@section('message', 'Sistem mendeteksi aktivitas pengiriman permintaan yang terlalu sering dalam waktu singkat. Demi menjaga stabilitas portal, silakan tunggu beberapa saat sebelum mencoba kembali.')
@section('button_url', url('/'))
@section('button_text', 'Kembali ke Beranda')
