@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Rekapitulasi Transaksi') }}</div>

                <div class="card-body">
                    @if (isset($recapData))
                        <p><strong>Total Amount:</strong> Rp. {{ number_format($recapData['total_amount'], 0, ',', '.') }}</p>
                        <p><strong>Total Nett:</strong> Rp. {{ number_format($recapData['total_nett'], 0, ',', '.') }}</p>

                        <p><strong>Jumlah Transaksi Berdasarkan Metode Pembayaran:</strong></p>
                        <ul>
                            @forelse ($recapData['payment_methods'] as $method => $count)
                                <li>{{ $method }}: {{ $count }}</li>
                            @empty
                                <li>Tidak ada data metode pembayaran.</li>
                            @endforelse
                        </ul>

                        <p><strong>Jumlah Transaksi Berdasarkan Status:</strong></p>
                        <ul>
                            @forelse ($recapData['transaction_statuses'] as $status => $count)
                                <li>{{ $status }}: {{ $count }}</li>
                            @empty
                                <li>Tidak ada data status transaksi.</li>
                            @endforelse
                        </ul>
                    @else
                        <p>Gagal mengambil data rekapitulasi.</p>
                    @endif
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection