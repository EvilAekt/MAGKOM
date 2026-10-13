@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')
@section('code', '419')
@section('code_color', '#D97706')
@section('icon_bg', '#FEF3C7')
@section('icon_color', '#92400E')

@section('icon')
<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"></circle>
    <polyline points="12 6 12 12 16 14"></polyline>
</svg>
@endsection

@section('headline', 'Sesi Anda Telah Kedaluwarsa')
@section('message', 'Halaman ini telah berada dalam kondisi tidak aktif cukup lama demi keamanan. Silakan muat ulang halaman atau masuk kembali ke akun Anda.')
@section('button_url', route('login'))
@section('button_text', 'Masuk Kembali')
