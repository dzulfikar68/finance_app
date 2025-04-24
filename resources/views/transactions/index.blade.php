@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Data Transaksi') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('transactions.index') }}" method="GET" class="mb-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="refunded" {{ $filter['status'] == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        <option value="settlement" {{ $filter['status'] == 'settlement' ? 'selected' : '' }}>Settlement</option>
                                        <option value="cancel" {{ $filter['status'] == 'cancel' ? 'selected' : '' }}>Cancel</option>
                                        <option value="timeout" {{ $filter['status'] == 'timeout' ? 'selected' : '' }}>Timeout</option>
                                        <option value="expire" {{ $filter['status'] == 'expire' ? 'selected' : '' }}>Expire</option>
                                        {{-- Tambahkan opsi status lain jika ada --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_method">Metode Pembayaran</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="">Semua Metode</option>
                                        <option value="CASH" {{ $filter['payment_method'] == 'CASH' ? 'selected' : '' }}>Cash</option>
                                        {{-- Tambahkan opsi metode pembayaran lain jika ada --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $filter['start_date'] }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $filter['end_date'] }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset Filter</a>
                        <a href="{{ route('transactions.export') }}" class="btn btn-success">Ekspor ke Excel</a>
                        <a href="{{ route('transactions.recap') }}" class="btn btn-info float-right">Lihat Rekap Transaksi</a>
                        <a href="{{ route('transactions.visualize') }}" class="btn btn-dark float-right ml-2">Lihat Visualisasi Data</a>
                    </form>

                    @if (count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><a href="{{ route('transactions.index', ['sort_column' => 'detail.order_id', 'sort_direction' => $sortColumn == 'detail.order_id' && $sortDirection == 'asc' ? 'desc' : 'asc'] + request()->except('sort_column', 'sort_direction')) }}">Order ID {!! $sortColumn == 'detail.order_id' ? ($sortDirection == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : '' !!}</a></th>
                                        <th>Nama Produk</th>
                                        <th><a href="{{ route('transactions.index', ['sort_column' => 'payment.method', 'sort_direction' => $sortColumn == 'payment.method' && $sortDirection == 'asc' ? 'desc' : 'asc'] + request()->except('sort_column', 'sort_direction')) }}">Metode Pembayaran {!! $sortColumn == 'payment.method' ? ($sortDirection == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : '' !!}</a></th>
                                        <th>Jumlah Pembayaran</th>
                                        <th><a href="{{ route('transactions.index', ['sort_column' => 'detail.transaction_status', 'sort_direction' => $sortColumn == 'detail.transaction_status' && $sortDirection == 'asc' ? 'desc' : 'asc'] + request()->except('sort_column', 'sort_direction')) }}">Status {!! $sortColumn == 'detail.transaction_status' ? ($sortDirection == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : '' !!}</a></th>
                                        <th><a href="{{ route('transactions.index', ['sort_column' => 'time.timestamp', 'sort_direction' => $sortColumn == 'time.timestamp' && $sortDirection == 'asc' ? 'desc' : 'asc'] + request()->except('sort_column', 'sort_direction')) }}">Waktu Transaksi {!! $sortColumn == 'time.timestamp' ? ($sortDirection == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : '' !!}</a></th>
                                        <th>Waktu Refund</th>
                                        <th>Jumlah Refund</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $transaction)
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
                                    @empty
                                        <tr><td colspan="8">Tidak ada data transaksi yang sesuai dengan filter.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Silakan gunakan form filter di atas untuk melihat data transaksi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection