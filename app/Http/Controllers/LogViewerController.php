<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class LogViewerController extends Controller
{
    private $logPath;
    private $logLevels = [
        'all' => 'All',
        'emergency' => 'Emergency',
        'alert' => 'Alert',
        'critical' => 'Critical',
        'error' => 'Error',
        'warning' => 'Warning',
        'notice' => 'Notice',
        'info' => 'Info',
        'debug' => 'Debug'
    ];

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    /**
     * Hiển thị danh sách các file log với phân trang
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $dateFilter = $request->get('date', '');
        $dateFilter = $dateFilter ? Carbon::parse($dateFilter)->format('d-m-Y') : '';

        $logFiles = $this->getLogFiles($dateFilter);

        // Phân trang cho danh sách file
        $perPage = 10;
        $currentItems = array_slice($logFiles, ($page - 1) * $perPage, $perPage);

        $paginatedFiles = new LengthAwarePaginator(
            $currentItems,
            count($logFiles),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
        return view('log-viewer.index', [
            'logFiles' => $paginatedFiles,
            'dateFilter' => $dateFilter,
            'availableDates' => $this->getAvailableDates()
        ]);
    }

    /**
     * Lấy chi tiết entries của một file log cụ thể với phân trang
     */
    public function show(Request $request, $date)
    {
        $level = $request->get('level', 'all');
        $search = $request->get('search', '');
        $page = $request->get('page', 1);


        $logFile = $this->getLogFilePath($date);

        if (!File::exists($logFile)) {
            abort(404, 'Log file not found');
        }

        $content = File::get($logFile);
        $allEntries = $this->parseLogEntries($content, $level, $search);
        $allLevelEntries = $this->parseLogEntries($content, 'all', $search);

        $levelCounts = [];

        foreach ($allLevelEntries as $entry) {
            $entryLevel = $entry['level'];
            if (!isset($levelCounts[$entryLevel])) {
                $levelCounts[$entryLevel] = 0;
            }
            $levelCounts[$entryLevel]++;
        }

        // Phân trang cho entries
        $perPage = 10;
        $currentItems = array_slice($allEntries, ($page - 1) * $perPage, $perPage);

        $paginatedEntries = new LengthAwarePaginator(
            $currentItems,
            count($allEntries),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );

        $fileInfo = $this->getFileInfo($logFile, $date);

        return view('log-viewer.show', [
            'entries' => $paginatedEntries,
            'fileInfo' => $fileInfo,
            'level' => $level,
            'search' => $search,
            'date' => $date,
            'logLevels' => $this->logLevels,
            'totalEntries' => count($allEntries),
            'levelCounts' => $levelCounts
        ]);
    }

    /**
     * API endpoint để lấy entries theo AJAX với phân trang
     */
    public function getEntriesByDate(Request $request, $date)
    {
        $level = $request->get('level', 'all');
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $logFile = $this->getLogFilePath($date);

        if (!File::exists($logFile)) {
            return response()->json([
                'entries' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total' => 0,
                    'per_page' => $perPage,
                    'last_page' => 1
                ]
            ]);
        }

        $content = File::get($logFile);
        $allEntries = $this->parseLogEntries($content, $level, $search);

        // Phân trang
        $total = count($allEntries);
        $lastPage = ceil($total / $perPage);
        $currentItems = array_slice($allEntries, ($page - 1) * $perPage, $perPage);

        return response()->json([
            'entries' => $currentItems,
            'pagination' => [
                'current_page' => (int) $page,
                'total' => $total,
                'per_page' => (int) $perPage,
                'last_page' => $lastPage
            ]
        ]);
    }

    /**
     * Lấy danh sách file log với filter theo ngày
     */
    private function getLogFiles($dateFilter = '')
    {
        $logFiles = [];

        // Lấy tất cả file log theo định dạng dd-mm-yyyy.log
        $files = glob($this->logPath . '/*.log');

        foreach ($files as $logFile) {
            if (!File::exists($logFile)) continue;

            $filename = basename($logFile);

            // Chỉ xử lý file có định dạng dd-mm-yyyy.log
            if (!preg_match('/^\d{2}-\d{2}-\d{4}\.log$/', $filename)) {
                continue;
            }

            $fileInfo = $this->getFileInfoFromPath($logFile, $filename);

            // Filter theo ngày nếu có
            if (!empty($dateFilter) && $fileInfo['date'] !== $dateFilter) {
                continue;
            }

            $logFiles[] = $fileInfo;
        }

        // Sắp xếp theo ngày mới nhất
        usort($logFiles, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $logFiles;
    }

    /**
     * Lấy thông tin file từ đường dẫn
     */
    private function getFileInfoFromPath($logFile, $filename)
    {
        // Xác định ngày từ tên file (dd-mm-yyyy.log)
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})\.log$/', $filename, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            $date = $day . '-' . $month . '-' . $year;
        } else {
            $date = 'unknown';
        }

        $fileSize = File::exists($logFile) ? File::size($logFile) : 0;
        $lastModified = File::exists($logFile) ? File::lastModified($logFile) : 0;

        // Đếm số entries trong file
        $entryCount = 0;
        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $entries = $this->parseLogEntries($content, 'all', '');
            $entryCount = count($entries);
        }

        return [
            'filename' => $filename,
            'date' => $date,
            'formatted_date' => $date,
            'size' => $this->formatBytes($fileSize),
            'size_bytes' => $fileSize,
            'last_modified' => $lastModified > 0 ? Carbon::createFromTimestamp($lastModified)->format('d-m-Y H:i:s') : 'N/A',
            'entry_count' => $entryCount,
            'path' => $logFile
        ];
    }

    /**
     * Lấy thông tin chi tiết của file
     */
    private function getFileInfo($logFile, $date)
    {
        $filename = basename($logFile);
        return $this->getFileInfoFromPath($logFile, $filename);
    }

    /**
     * Lấy đường dẫn file log theo ngày
     */
    private function getLogFilePath($date)
    {
        // Format: dd-mm-yyyy.log
        return $this->logPath . '/' . $date . '.log';
    }

    /**
     * Lấy danh sách các ngày có sẵn
     */
    private function getAvailableDates()
    {
        $dates = [];
        $logFiles = $this->getLogFiles();

        foreach ($logFiles as $file) {
            if ($file['date'] !== 'unknown') {
                $dates[] = [
                    'value' => $file['date'],
                    'label' => $file['formatted_date']
                ];
            }
        }

        return $dates;
    }

    private function parseLogEntries($content, $filterLevel, $search = '')
    {
        $entries = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            // Parse log format: [2025-10-09 14:04:35] local.ERROR: [OPERATION FAILED] {...}
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): \[(.*?)\] (.*)/', $line, $matches)) {
                $timestamp = $matches[1];
                $level = strtolower($matches[2]);
                $operation = $matches[3];
                $jsonData = $matches[4];

                // Decode JSON data
                $context = json_decode($jsonData, true);
                $message = $operation;

                // Thêm thông tin từ context vào message
                if ($context) {
                    if (isset($context['exception']['message'])) {
                        $message .= ' - ' . $context['exception']['message'];
                    }
                    if (isset($context['route'])) {
                        $message .= ' [Route: ' . $context['route'] . ']';
                    }
                }

                $entry = [
                    'timestamp' => $timestamp,
                    'level' => $level,
                    'message' => $message,
                    'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                    'formatted_time' => Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->format('H:i:s'),
                    'level_class' => $this->getLevelClass($level)
                ];

                // Lọc theo level
                if ($filterLevel === 'all' || $level === $filterLevel) {
                    // Lọc theo từ khóa tìm kiếm
                    if (
                        empty($search) ||
                        stripos($entry['message'], $search) !== false ||
                        stripos($entry['context'], $search) !== false
                    ) {
                        $entries[] = $entry;
                    }
                }
            }
        }

        // Đảo ngược thứ tự để hiển thị mới nhất trước
        return array_reverse($entries);
    }

    private function getLevelClass($level)
    {
        $classes = [
            'emergency' => 'bg-red-900 text-white',
            'alert' => 'bg-red-700 text-white',
            'critical' => 'bg-red-600 text-white',
            'error' => 'bg-red-500 text-white',
            'warning' => 'bg-yellow-500 text-black',
            'notice' => 'bg-blue-500 text-white',
            'info' => 'bg-green-500 text-white',
            'debug' => 'bg-gray-500 text-white'
        ];

        return $classes[$level] ?? 'bg-gray-400 text-white';
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    public function download(Request $request)
    {
        $date = $request->get('date');
        $logFile = $this->getLogFilePath($date);

        if (!File::exists($logFile)) {
            abort(404, 'Log file not found');
        }

        return response()->download($logFile);
    }

    public function clear(Request $request)
    {
        $date = $request->get('date');
        $logFile = $this->getLogFilePath($date);

        if (File::exists($logFile)) {
            File::put($logFile, '');
            return redirect()->back()->with('success', 'Log file cleared successfully');
        }

        return redirect()->back()->with('error', 'Log file not found');
    }

    public function getLevels()
    {
        return $this->logLevels;
    }
}
