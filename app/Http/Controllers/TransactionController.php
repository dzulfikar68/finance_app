<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('transactions.index');
    }

    public function fetchData()
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user', // Asumsi username adalah email
            'password' => 'password' // Ganti dengan mekanisme penyimpanan password yang aman
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'];
            return view('transactions.list', ['transactions' => $data]);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi.');
        }
    }

    public function recap()
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user', // Asumsi username adalah email
            'password' => 'password' // Ganti dengan mekanisme penyimpanan password yang aman
        ]);

        if ($response->successful()) {
            $transactions = $response->json()['data'];
            $recapData = $this->processRecap($transactions);
            return view('transactions.recap', ['recapData' => $recapData]);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi untuk rekap.');
        }
    }

    private function processRecap($transactions)
    {
        $totalAmount = 0;
        $totalNett = 0;
        $paymentMethodCounts = [];
        $transactionStatusCounts = [];

        foreach ($transactions as $transaction) {
            if (isset($transaction['payment']['amount'])) {
                $totalAmount += $transaction['payment']['amount'];
            }
            if (isset($transaction['payment']['nett'])) {
                $totalNett += $transaction['payment']['nett'];
            }
            if (isset($transaction['payment']['method'])) {
                $method = $transaction['payment']['method'];
                $paymentMethodCounts[$method] = ($paymentMethodCounts[$method] ?? 0) + 1;
            }
            if (isset($transaction['detail']['transaction_status'])) {
                $status = $transaction['detail']['transaction_status'];
                $transactionStatusCounts[$status] = ($transactionStatusCounts[$status] ?? 0) + 1;
            }
        }

        return [
            'total_amount' => $totalAmount,
            'total_nett' => $totalNett,
            'payment_methods' => $paymentMethodCounts,
            'transaction_statuses' => $transactionStatusCounts,
        ];
    }
}
