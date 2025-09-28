<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TransactionExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        
    }


    public function exportCsv(Request $request)
    {
        $query = Transaction::where('user_id', auth()->user()->id)
            ->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);

        if( !empty($request->input('category')) ) {
            $query->where('category_id', $request->input('category'));
            $query->with('category');
        }

        if( !empty($request->input('type')) ) {
            $query->where('type', $request->input('type'));
        }

        $transactions = $query->get();

        $csv = "Date,Type,Category,Amount,Description\n";
        foreach ($transactions as $transaction) {
            $type = $transaction->type == 1 ? 'Income' : 'Expense';
            $category = $transaction->category ? $transaction->category->name : '';
            $amount = $transaction->amount;
            $csv .= "{$transaction->date},{$type},{$category},{$amount},{$transaction->description}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ]);
    }
}
