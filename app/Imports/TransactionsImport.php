<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TransactionsImport implements ToModel, WithHeadingRow, WithMapping
{

    private $userId;

    public function __construct($userId){
        $this->userId = $userId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Transaction([
            'user_id' => $this->userId,
            'type' => $row['type'],
            'amount' => $row['amount'],
            'date' => $row['date'],
            'description' => $row['description'],
            'category_id' => $row['category']
        ]);
    }

    public function map($row): array
    {
        // date formatting
        $formats = [
            'n/d/Y',  // 9/22/2025
            'n/j/Y',  // 9/2/2025
            'm/d/Y',  // 09/22/2025
            'm/j/Y',  // 09/2/2025
            'Y-m-d',  // 2025-09-22
        ];

        foreach ($formats as $format) {
            $formattedDate = Carbon::createFromFormat($format, $row['date'])->format('Y-m-d');
            break; // Exit the loop if parsing is successful
        }

        $row['date'] = $formattedDate;

        // type formatting
        switch (strtolower($row['type'])) {
            case 'income':
                $row['type'] = 1;
                break;
            case 'expense':
                $row['type'] = 2;
                break;
        }

        // category formatting
        $category = Category::firstorCreate([
            'type' => $row['type'],
            'name' => $row['category'],
            'user_id' => $this->userId
        ]);
        $row['category'] = $category->id;

        // cast to decimal amount
        $row['amount'] = (float) str_replace(',','',$row['amount']);

        return $row;
    }
}
