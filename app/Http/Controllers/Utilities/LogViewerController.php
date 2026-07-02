<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogViewerController extends Controller
{
    public function index(Request $request): View
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return view('utilities.log-viewer', ['lines' => [], 'logSize' => 0]);
        }

        $maxLines = min((int) $request->input('lines', 500), 5000);
        $logSize = filesize($logFile);

        exec("tail -n {$maxLines} " . escapeshellarg($logFile) . " 2>&1", $output);
        $lines = $output;

        return view('utilities.log-viewer', compact('lines', 'logSize'));
    }
}
