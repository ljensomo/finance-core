<?php

namespace App\Http\Controllers;

use App\Imports\TransactionsImport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $transactions = Transaction::with('category')->where('user_id', Auth::id())
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc')->get();
                            
        return response()->json($transactions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $transaction = new Transaction();
        $transaction->user_id = Auth::id();
        $transaction->type = $request->input('type');
        $transaction->amount = $request->input('amount');
        $transaction->date = $request->input('date');
        $transaction->description = $request->input('description');
        $transaction->category_id = $request->input('category_id');
        $transaction->save();

        return response()->json($transaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->date = $request->input('date');
        $transaction->type = $request->input('type');
        $transaction->amount = $request->input('amount');
        $transaction->description = $request->input('description');
        $transaction->category_id = $request->input('category_id');
        $transaction->save();

        return response()->json('Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json('Transaction deleted successfully.');
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        Excel::import(new TransactionsImport(Auth::id()), $request->file('file'));

        return response()->json('Transactions imported successfully.');
    }
}
