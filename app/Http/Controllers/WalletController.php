<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getBalance()
    {
        $user = auth()->user();
        $wallet = $user->wallet; // Assuming the User model has a wallet relationship


        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'balance' => $wallet->balance
        ], 200);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string', // e.g., bank transfer, PayPal, etc.
            'account_details' => 'required|string', // e.g., bank account number, Pay
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        if ($wallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        // Process the withdrawal logic here
        // For example, deduct the amount from the wallet balance
        $wallet->balance -= $request->amount;
        $wallet->total_withdrawn += $request->amount;
        $wallet->last_transaction_at = now();
        $wallet->status = 'active'; // Assuming the wallet remains active after withdrawal
        $wallet->save();

        Transaction::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'type' => 'credit',
            'category' => 'withdrawal',
            'payment_method' => $request->payment_method,
            'account_details' => $request->account_details,
            'amount' => $request->amount,
            'balance_before' => $wallet->balance + $request->amount,
            'balance_after' => $wallet->balance,
            'status' => 'completed',
            'description' => 'Withdrawal of amount: ' . $request->amount,
        ]);

        // Optionally, create a transaction record for this withdrawal
        // ...

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal successful',
            'new_balance' => $wallet->balance
        ], 200);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)
            ->where('tenant_id', $user->tenant_id)
            ->where('category', 'withdrawal')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ], 200);
    }
}
