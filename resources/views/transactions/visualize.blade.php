@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Visualisasi Data Transaksi') }}</div>

                <div class="card-body">
                    @if (isset($recapData))
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Jumlah Transaksi per Metode Pembayaran</h3>
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h3>Jumlah Transaksi per Status</h3>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>Total Amount vs Total Nett</h3>
                                <canvas id="amountNettChart"></canvas>
                            </div>
                            {{-- Anda bisa menambahkan visualisasi lain di sini --}}
                        </div>
                    @else
                        <p>Gagal mengambil data rekapitulasi untuk visualisasi.</p>
                    @endif
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Transaksi</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const recapData = @json($recapData);

    // Payment Method Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodChart = new Chart(paymentMethodCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(recapData.payment_methods),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: Object.values(recapData.payment_methods),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(recapData.transaction_statuses),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: Object.values(recapData.transaction_statuses),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                    // Tambahkan warna lain jika ada status lain
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                    // Tambahkan warna lain jika ada status lain
                ],
                borderWidth: 1
            }]
        },
        options: {
            // Opsi lain untuk pie chart
        }
    });

    // Amount vs Nett Chart
    const amountNettCtx = document.getElementById('amountNettChart').getContext('2d');
    const amountNettChart = new Chart(amountNettCtx, {
        type: 'bar',
        data: {
            labels: ['Total Amount', 'Total Nett'],
            datasets: [{
                label: 'Nilai (Rp)',
                data: [recapData.total_amount, recapData.total_nett],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.7)', // Blue
                    'rgba(40, 167, 69, 0.7)'  // Green
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection