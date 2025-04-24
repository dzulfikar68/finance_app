<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    protected $transactions;

    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->transactions)->map(function ($transaction) {
            return [
                $transaction['detail']['order_id'] ?? '-',
                $transaction['product']['name'] ?? '-',
                $transaction['payment']['method'] ?? '-',
                $transaction['payment']['amount'] ?? '-',
                $transaction['detail']['transaction_status'] ?? '-',
                isset($transaction['time']['timestamp']) ? date('Y-m-d H:i:s', $transaction['time']['timestamp'] / 1000) : '-',
                isset($transaction['detail']['refund_time']) ? date('Y-m-d H:i:s', $transaction['detail']['refund_time'] / 1000) : '-',
                $transaction['detail']['refund_amount'] ?? '-',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Nama Produk',
            'Metode Pembayaran',
            'Jumlah Pembayaran',
            'Status',
            'Waktu Transaksi',
            'Waktu Refund',
            'Jumlah Refund',
        ];
    }
}