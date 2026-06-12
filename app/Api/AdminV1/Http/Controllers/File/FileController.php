<?php

namespace App\Api\AdminV1\Http\Controllers\File;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Admin\Services\File\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Upload file(s) and convert to base64
     * Supports both single file ('file') and multiple files ('files[]')
     */
    public function upload(Request $request)
    {
        // Check if multiple files are being uploaded
        if ($request->hasFile('files')) {
            return $this->uploadMultiple($request);
        }

        // Single file upload (backward compatible)
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        try {
            $file = $request->file('file');
            $result = $this->processSingleFile($file);

            return response()->json([
                'status' => 200,
                'message' => __('file.upload_success'),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => __('file.upload_failed', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Upload multiple files
     */
    private function uploadMultiple(Request $request)
    {
        $request->validate([
            'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB per file
        ]);

        try {
            $files = $request->file('files');
            $results = [];

            foreach ($files as $file) {
                try {
                    $result = $this->processSingleFile($file);
                    $results[] = $result;
                } catch (\Exception $e) {
                    // Log error but continue with other files
                    Log::error('File upload error: ' . $e->getMessage());
                    $results[] = [
                        'error' => $e->getMessage(),
                        'name' => $file->getClientOriginalName(),
                    ];
                }
            }

            $successCount = count(array_filter($results, function ($result) {
                return !isset($result['error']);
            }));

            return response()->json([
                'status' => 200,
                'message' => __('file.upload_success_count', ['count' => $successCount]),
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => __('file.upload_failed', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Process a single file upload
     */
    private function processSingleFile($file)
    {
        // Set folder for uploads
        $this->fileService->setFolder('images/' . date('Y/m'));

        // Upload file
        $this->fileService->setFile($file);
        $path = $this->fileService->upload()->getInstance();

        // Compress image and convert to base64
        $fullPath = storage_path('app/public/uploads/' . str_replace('public/uploads/', '', $path));

        $base64 = null;
        if (file_exists($fullPath)) {
            // Get image info
            $imageInfo = getimagesize($fullPath);
            $mimeType = $imageInfo['mime'] ?? $file->getMimeType();

            // Try to compress using GD library
            if (function_exists('imagecreatefromjpeg') || function_exists('imagecreatefrompng')) {
                $compressed = $this->compressImage($fullPath, $mimeType);
                if ($compressed) {
                    $imageData = file_get_contents($fullPath);
                    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                }
            }

            // If compression failed, just read the file
            if (!$base64) {
                $imageData = file_get_contents($fullPath);
                $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }

        return [
            'path' => $path, // Relative path only
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'base64' => $base64,
        ];
    }

    /**
     * List files with pagination
     */
    public function list(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        try {
            // Get files from storage
            $disk = Storage::disk('uploads');
            $folder = 'images/';

            // Get all files recursively
            $allFiles = $disk->allFiles($folder);

            // Filter by search if provided
            if ($search) {
                $allFiles = array_filter($allFiles, function ($file) use ($search) {
                    return stripos(basename($file), $search) !== false;
                });
            }

            // Sort by modified time (newest first)
            usort($allFiles, function ($a, $b) use ($disk) {
                return $disk->lastModified($b) - $disk->lastModified($a);
            });

            // Paginate
            $total = count($allFiles);
            $lastPage = ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;
            $files = array_slice($allFiles, $offset, $perPage);

            // Format files data
            $filesData = array_map(function ($file) use ($disk) {
                $fullPath = $disk->path($file);
                // Return relative path only (no full URL)
                $relativePath = 'public/uploads/' . $file;

                return [
                    'id' => crc32($file), // Simple ID generation
                    'name' => basename($file),
                    'path' => $relativePath, // Relative path only
                    'size' => $disk->size($file),
                    'mime_type' => mime_content_type($fullPath),
                    'created_at' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                ];
            }, $files);

            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $filesData,
                'meta' => [
                    'current_page' => (int) $page,
                    'last_page' => $lastPage,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                ],
                'links' => [
                    'first' => $request->url() . '?page=1',
                    'last' => $request->url() . '?page=' . $lastPage,
                    'prev' => $page > 1 ? $request->url() . '?page=' . ($page - 1) : null,
                    'next' => $page < $lastPage ? $request->url() . '?page=' . ($page + 1) : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => __('file.error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Compress image using GD library
     */
    private function compressImage($filePath, $mimeType, $quality = 85)
    {
        try {
            $image = null;

            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($filePath);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $image = imagecreatefromwebp($filePath);
                    }
                    break;
            }

            if (!$image) {
                return false;
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Resize if too large (max width 1920px)
            $maxWidth = 1920;
            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = intval($height * ($maxWidth / $width));

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG
                if ($mimeType === 'image/png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Save compressed image
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($image, $filePath, $quality);
                    break;
                case 'image/png':
                    // PNG compression level (0-9, 9 is highest compression)
                    $pngQuality = 9 - round($quality / 10);
                    imagepng($image, $filePath, $pngQuality);
                    break;
                case 'image/gif':
                    imagegif($image, $filePath);
                    break;
                case 'image/webp':
                    if (function_exists('imagewebp')) {
                        imagewebp($image, $filePath, $quality);
                    }
                    break;
            }

            imagedestroy($image);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
