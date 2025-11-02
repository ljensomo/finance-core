<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ImportLogs;

class ImportLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $logs = ImportLogs::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }

}
