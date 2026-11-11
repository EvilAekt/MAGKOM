@extends('errors.layout')

@section('title', '429 - Akses Dibatasi Sementara')
@section('code', '429')
@section('code_color', '#DC2626')
@section('icon_bg', '#FEF2F2')
@section('icon_color', '#DC2626')

@section('icon')
<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
</svg>
@endsection

@section('headline', 'Akses Akun Dibatasi Sementara')
@section('message', 'Sistem keamanan mendeteksi terlalu banyak percobaan masuk dalam waktu singkat demi melindungi integritas akun dari upaya tidak sah (brute-force). Silakan tunggu sekitar 60 detik sebelum mencoba kembali, atau gunakan opsi pemulihan kata sandi.')
@section('button_url', route('login'))
@section('button_text', 'Kembali ke Halaman Login')
