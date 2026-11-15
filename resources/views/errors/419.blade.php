@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Halaman Telah Berakhir')
@section('message', 'Token keamanan formulir sudah kedaluwarsa karena halaman terlalu lama dibuka. Silakan muat ulang halaman lalu kirim ulang.')

@section('extra_action')
    <button type="button" onclick="window.location.reload()" class="btn-secondary">Muat Ulang Halaman</button>
@endsection
