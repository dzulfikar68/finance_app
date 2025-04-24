<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction; // Jika Anda menyimpan data lokal
use Maatwebsite\Excel\Facades\Excel; // Untuk ekspor Excel
use App\Exports\TransactionsExport; // Akan dibuat nanti

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

    public function index(Request $request)
    {
        $transactions = [];
        $filter = $request->only(['status', 'payment_method', 'start_date', 'end_date']);
        $filter = array_merge([
            'status' => '',
            'payment_method' => '',
            'start_date' => '',
            'end_date' => '',
        ], $filter);

        $sortColumn = $request->input('sort_column', 'time.timestamp');
        $sortDirection = $request->input('sort_direction', 'desc');

        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user',
            'password' => 'password'
        ]);

        if ($response->successful()) {
            $allTransactions = collect($response->json()['data']);
            $transactions = $this->applyFiltersAndSort($allTransactions, $filter, $sortColumn, $sortDirection);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi.');
        }

        return view('transactions.index', [
            'transactions' => $transactions,
            'filter' => $filter,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function fetchData(Request $request)
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user',
            'password' => 'password'
        ]);

        if ($response->successful()) {
            $allTransactions = collect($response->json()['data']);
            $filter = $request->only(['status', 'payment_method', 'start_date', 'end_date']);
            $sortColumn = $request->input('sort_column', 'time.timestamp');
            $sortDirection = $request->input('sort_direction', 'desc');

            $transactions = $this->applyFiltersAndSort($allTransactions, $filter, $sortColumn, $sortDirection);

            return view('transactions.list', [
                'transactions' => $transactions,
                'filter' => $filter,
                'sortColumn' => $sortColumn,
                'sortDirection' => $sortDirection,
            ]);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi.');
        }
    }

    private function applyFiltersAndSort($transactions, $filter, $sortColumn, $sortDirection)
    {
        if ($filter['status']) {
            $transactions = $transactions->filter(function ($transaction) use ($filter) {
                return isset($transaction['detail']['transaction_status']) && $transaction['detail']['transaction_status'] == $filter['status'];
            });
        }

        if ($filter['payment_method']) {
            $transactions = $transactions->filter(function ($transaction) use ($filter) {
                return isset($transaction['payment']['method']) && $transaction['payment']['method'] == $filter['payment_method'];
            });
        }

        if ($filter['start_date'] && $filter['end_date']) {
            $transactions = $transactions->filter(function ($transaction) use ($filter) {
                $timestamp = $transaction['time']['timestamp'] / 1000;
                $startDate = strtotime($filter['start_date'] . ' 00:00:00');
                $endDate = strtotime($filter['end_date'] . ' 23:59:59');
                return $timestamp >= $startDate && $timestamp <= $endDate;
            });
        } elseif ($filter['start_date']) {
            $transactions = $transactions->filter(function ($transaction) use ($filter) {
                $timestamp = $transaction['time']['timestamp'] / 1000;
                $startDate = strtotime($filter['start_date'] . ' 00:00:00');
                return $timestamp >= $startDate;
            });
        } elseif ($filter['end_date']) {
            $transactions = $transactions->filter(function ($transaction) use ($filter) {
                $timestamp = $transaction['time']['timestamp'] / 1000;
                $endDate = strtotime($filter['end_date'] . ' 23:59:59');
                return $timestamp <= $endDate;
            });
        }

        return $transactions->sortBy(function ($transaction) use ($sortColumn) {
            // Handle nested keys for sorting
            $keys = explode('.', $sortColumn);
            $value = $transaction;
            foreach ($keys as $key) {
                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return null; // Or some default value if the key doesn't exist
                }
            }
            return $value;
        }, null, $sortDirection === 'desc');
    }

    public function recap()
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user',
            'password' => 'password'
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

    public function visualize()
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user',
            'password' => 'password'
        ]);

        if ($response->successful()) {
            $transactions = $response->json()['data'];
            $recapData = $this->processRecap($transactions);
            return view('transactions.visualize', ['recapData' => $recapData]);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi untuk visualisasi.');
        }
    }

    public function export()
    {
        $user = Auth::user();
        $response = Http::post('https://login-bir3msoyja-et.a.run.app', [
            'user' => 'user',
            'password' => 'password'
        ]);

        if ($response->successful()) {
            $transactionsData = collect($response->json()['data'])->values()->toArray();
            return Excel::download(new TransactionsExport($transactionsData), 'transactions.xlsx');
        } else {
            return redirect()->route('transactions.index')->with('error', 'Gagal mengambil data transaksi untuk diekspor.');
        }
    }
}
