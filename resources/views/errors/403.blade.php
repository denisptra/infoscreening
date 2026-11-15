@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('message', 'Maaf, halaman yang Anda cari tidak tersedia atau Anda tidak memiliki akses ke halaman tersebut.')

@section('extra_action')
    <button type="button" onclick="history.back()" class="btn-secondary">Halaman Sebelumnya</button>
@endsection
