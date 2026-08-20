<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    /**
     * Display the logs management page.
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logEntries = [];
        $logSize = 0;
        $logExists = File::exists($logFile);

        if ($logExists) {
            $logSize = File::size($logFile);

            // Read last portion of log file (max 500KB to avoid memory issues)
            $maxBytes = 500 * 1024;
            $content = '';

            if ($logSize > $maxBytes) {
                $fp = fopen($logFile, 'r');
                fseek($fp, -$maxBytes, SEEK_END);
                fgets($fp); // Skip partial first line
                $content = fread($fp, $maxBytes);
                fclose($fp);
            } else {
                $content = File::get($logFile);
            }

            // Parse log entries
            $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}\.?\d*[+-]?\d{0,4})\]\s+(\w+)\.(\w+):\s*(.*?)(?=\[\d{4}-\d{2}-\d{2}[T ]|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $logEntries[] = [
                    'timestamp' => $match[1],
                    'environment' => $match[2],
                    'level' => strtolower($match[3]),
                    'message' => trim($match[4]),
                ];
            }

            // Reverse to show newest first
            $logEntries = array_reverse($logEntries);

            // Filter by level if requested
            $levelFilter = $request->get('level');
            if ($levelFilter && $levelFilter !== 'all') {
                $logEntries = array_filter($logEntries, function ($entry) use ($levelFilter) {
                    return $entry['level'] === $levelFilter;
                });
                $logEntries = array_values($logEntries);
            }

            // Filter by search query
            $search = $request->get('search');
            if ($search) {
                $logEntries = array_filter($logEntries, function ($entry) use ($search) {
                    return stripos($entry['message'], $search) !== false
                        || stripos($entry['timestamp'], $search) !== false;
                });
                $logEntries = array_values($logEntries);
            }
        }

        // Count by level
        $levelCounts = [
            'emergency' => 0,
            'alert' => 0,
            'critical' => 0,
            'error' => 0,
            'warning' => 0,
            'notice' => 0,
            'info' => 0,
            'debug' => 0,
        ];
        foreach ($logEntries as $entry) {
            if (isset($levelCounts[$entry['level']])) {
                $levelCounts[$entry['level']]++;
            }
        }

        $totalEntries = count($logEntries);

        // Paginate manually
        $perPage = 50;
        $page = max(1, (int) $request->get('page', 1));
        $totalPages = max(1, ceil($totalEntries / $perPage));
        $page = min($page, $totalPages);
        $logEntries = array_slice($logEntries, ($page - 1) * $perPage, $perPage);

        return view('admin.logs.index', compact(
            'logEntries',
            'logSize',
            'logExists',
            'levelCounts',
            'totalEntries',
            'page',
            'totalPages',
            'perPage'
        ));
    }

    /**
     * Clear the Laravel log file.
     */
    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return redirect()->route('admin.logs.index')
            ->with('success', 'Log file cleared successfully.');
    }

    /**
     * Download the Laravel log file.
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return redirect()->route('admin.logs.index')
                ->with('error', 'Log file not found.');
        }

        return response()->download($logFile, 'laravel-' . date('Y-m-d-His') . '.log');
    }
}
