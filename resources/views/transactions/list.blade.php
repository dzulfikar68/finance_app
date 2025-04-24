@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Daftar Transaksi') }}</div>

                <div class="card-body">
                    @if (count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Nama Produk</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Jumlah Pembayaran</th>
                                        <th>Status</th>
                                        <th>Waktu Transaksi</th>
                                        <th>Waktu Refund</th>
                                        <th>Jumlah Refund</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction['detail']['order_id'] ?? '-' }}</td>
                                            <td>{{ $transaction['product']['name'] ?? '-' }}</td>
                                            <td>{{ $transaction['payment']['method'] ?? '-' }}</td>
                                            <td>{{ $transaction['payment']['amount'] ?? '-' }}</td>
                                            <td>{{ $transaction['detail']['transaction_status'] ?? '-' }}</td>
                                            <td>{{ isset($transaction['time']['timestamp']) ? date('Y-m-d H:i:s', $transaction['time']['timestamp'] / 1000) : '-' }}</td>
                                            <td>{{ isset($transaction['detail']['refund_time']) ? date('Y-m-d H:i:s', $transaction['detail']['refund_time'] / 1000) : '-' }}</td>
                                            <td>{{ $transaction['detail']['refund_amount'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Tidak ada data transaksi.</p>
                    @endif
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection