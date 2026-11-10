@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')
@section('code_color', '#0641CC')
@section('icon_bg', '#E6EEFF')
@section('icon_color', '#0641CC')

@section('icon')
<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
</svg>
@endsection

@section('headline', 'Halaman Tidak Ditemukan')
@section('message', 'Tautan yang Anda tuju mungkin salah ketik, telah dipindahkan, atau periode magang terkait sudah tidak tersedia.')
@section('button_url', url('/'))
@section('button_text', 'Kembali ke Beranda')
