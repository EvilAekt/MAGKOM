@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('code_color', '#DC2626')
@section('icon_bg', '#FEE2E2')
@section('icon_color', '#991B1B')

@section('icon')
<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
</svg>
@endsection

@section('headline', 'Akses Ditolak')
@section('message', 'Anda tidak memiliki hak akses yang memadai untuk membuka halaman atau memproses tindakan ini. Pastikan Anda masuk dengan peran akun yang sesuai.')
@section('button_url', url('/'))
@section('button_text', 'Kembali ke Beranda')
