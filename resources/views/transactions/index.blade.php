@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Data Transaksi') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p>Selamat datang di halaman data transaksi.</p>
                    <a href="{{ route('transactions.fetch') }}" class="btn btn-primary">Lihat Data Transaksi</a>
                    <a href="{{ route('transactions.recap') }}" class="btn btn-info">Lihat Rekap Transaksi</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection