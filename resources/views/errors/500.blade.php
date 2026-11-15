@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Kesalahan Pada Server')
@section('message', 'Maaf, sistem mengalami kendala teknis saat memproses permintaan Anda. Silakan muat ulang beberapa saat lagi atau hubungi tim redaksi jika masalah berlanjut.')

@section('extra_action')
    <button type="button" onclick="window.location.reload()" class="btn-secondary">Coba Lagi</button>
@endsection
