@extends('errors.layout')

@section('code', '405')
@section('title', 'Metode Permintaan Tidak Diizinkan')
@section('message', 'Metode HTTP yang digunakan untuk mengakses halaman ini tidak didukung. Silakan gunakan tombol atau formulir resmi di dalam situs.')

@section('extra_action')
    <button type="button" onclick="history.back()" class="btn-secondary">Kembali</button>
@endsection
