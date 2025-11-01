<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Client;
use Google\Service\Sheets;
use App\Models\ImportLogs;
use App\Models\Transaction;
use App\Models\Category;

class GoogleSheetsController extends Controller
{
    protected function getGoogleClient()
    {
        $client = new Client();
        $client->setApplicationName('Finance Core Integration');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path(env('GOOGLE_SERVICE_ACCOUNT_JSON')));
        $client->setAccessType('offline');

        return $client;
    }

    public function getTransactions($startingRow)
    {
        $client = $this->getGoogleClient();
        $service = new Sheets($client);

        $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
        $range = env('GOOGLE_SHEET_NAME') . '!A' . $startingRow . ':E'; // Adjust range as needed

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues(); // array

        return $values;
    }

    public function writeToSheet(Request $request)
    {
        $client = $this->getGoogleClient();
        $service = new Sheets($client);

        $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
        $range = env('GOOGLE_SHEET_NAME') . '!A1'; // Starting cell

        $values = [
            [$request->input('date'), $request->input('amount'), $request->input('category')]
        ];

        $body = new \Google\Service\Sheets\ValueRange([
            'values' => $values
        ]);

        $params = ['valueInputOption' => 'RAW'];
        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

        return response()->json([
            'status' => 'success',
            'updatedRange' => $result->getUpdates()->getUpdatedRange()
        ]);
    }

    public function syncTransactions()
    {
        $total_rows = 0;
        $total_failed = 0;
        $imported_rows = 0;
        $latestLog = ImportLogs::where('user_id', Auth::id())->orderBy('updated_at', 'desc')
                ->latest()
                ->first();

        if(!$latestLog){
            $startingRow = 2;
        }else{
            $startingRow = $latestLog->last_row_number + 1;
        }

        $last_row = $startingRow;

        $transactions = $this->getTransactions($startingRow);

        if($transactions){
            foreach($transactions as $index => $row){
                $category = Category::where('user_id', Auth::id())->where('name', $row[3])->first();
                
                if(!$category){
                    $total_rows++;
                    $total_failed++;
                    continue;
                }

                $cleanAmount = str_replace(['₱', ','], '', $row[2]);

                Transaction::create([
                    'user_id' => Auth::id(),
                    'date' => date('Y-m-d', strtotime($row[1])),
                    'amount' => floatval($cleanAmount),
                    'category_id' => $category->id,
                    'type' => 2,
                    'description' => $row[4],
                ]);

                $total_rows++;
                $imported_rows++;
                $last_row++;
            }

            ImportLogs::create([
                'user_id' => Auth::id(),
                'total_rows' => $total_rows,
                'rows_imported' => $imported_rows,
                'rows_failed' => $total_failed,
                'last_row_number' => $last_row - 1,
            ]);
        }

        return response()->json([
            'total_rows' => $total_rows,
            'rows_imported' => $imported_rows,
            'rows_failed' => $total_failed,
        ]);
    }
}
