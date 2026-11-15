@extends('errors.layout')

@section('code', '503')
@section('title', 'Situs Sedang Dalam Pemeliharaan')
@section('message', 'Infoscreening sedang melakukan pembaruan sistem singkat. Silakan kembali lagi beberapa menit lagi.')

@section('extra_action')
    <button type="button" onclick="window.location.reload()" class="btn-secondary">Muat Ulang</button>
@endsection
