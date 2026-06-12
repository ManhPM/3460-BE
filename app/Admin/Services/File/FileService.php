<?php

namespace App\Admin\Services\File;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    private $disk = 'uploads';

    private $folder = '/';

    private $folderPrefix = 'public/uploads/';

    private $file;

    private $instance;

    private $status = true;

    public function setDisk($disk)
    {
        $this->disk = $disk;
        return $this;
    }

    public function setFolder($folder)
    {
        $this->folder = Str::finish($folder, '/');
        return $this;
    }

    public function setFolderForUser($path = '/')
    {
        $path = $path == '/' ? '/' : '/' . Str::finish($path, '/');
        return $this->setFolder('users/' . auth()->user()->id . $path);
    }

    public function setFolderPrefix($folderPrefix)
    {
        $this->folderPrefix = Str::finish($folderPrefix, '/');
        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function upload()
    {
        $path = $this->file->storeAs($this->folder, $this->file->hashName(), $this->disk);
        $this->instance = $this->folderPrefix . $path;
        return $this;
    }

    public function uploadFilepondEncode()
    {
        $file = json_decode($this->file, true);

        return $this->uploadFileBase64($file);
    }

    public function uploadCheckFilepondEncode($fileExists)
    {
        $file = json_decode($this->file, true);
        if (array_key_exists($file['id'], $fileExists)) {
            $this->instance = Str::after($fileExists[$file['id']], url('/'));
            return $this;
        }
        return $this->uploadFileBase64($file);
    }

    public function uploadFileBase64($files)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploadedFiles = [];

        foreach ($files as $file) {
            if (!$file || !is_string($file)) {
                continue;
            }

            // Detect extension
            $extension = $this->detectExtensionFromBase64($file);

            // Remove header nếu có
            $cleanBase64 = preg_replace('/^data:(.*?);base64,/', '', $file);

            $fileContent = base64_decode($cleanBase64);

            $pathFile = $this->folder . uniqid_real() . '.' . $extension;
            Storage::disk($this->disk)->put($pathFile, $fileContent);

            $uploadedFiles[] = $this->folderPrefix . $pathFile;
        }

        $this->instance = $uploadedFiles;

        return $this->instance;
    }


    public function uploadSingleFileBase64($file)
    {
        if (!$file || !is_string($file)) {
            return null;
        }

        $extension = $this->detectExtensionFromBase64($file);
        $cleanBase64 = preg_replace('/^data:(.*?);base64,/', '', $file);
        $fileContent = base64_decode($cleanBase64);

        $pathFile = $this->folder . uniqid_real() . '.' . $extension;
        Storage::disk($this->disk)->put($pathFile, $fileContent);

        return $this->folderPrefix . $pathFile;
    }

    private function detectExtensionFromBase64(string $base64): string
    {
        // Trường hợp base64 có header
        if (preg_match('/^data:(.*?);base64,/', $base64, $matches)) {
            $mime = $matches[1]; // vd: image/png
            return $this->mimeToExtension($mime);
        }

        // Base64 không có header → decode để phân tích mime
        $fileContent = base64_decode($base64);
        if (!$fileContent) return 'bin';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $fileContent);
        finfo_close($finfo);

        return $this->mimeToExtension($mime);
    }

    private function mimeToExtension(?string $mime): string
    {
        if (!$mime) return 'bin';

        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/bmp' => 'bmp',
            'image/svg+xml' => 'svg',

            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        return $map[$mime] ?? 'bin';
    }


    public function move($pathFile, $newPath)
    {
        $newPath = $newPath . basename($pathFile);
        Storage::disk($this->disk)->move($pathFile, $newPath . basename($pathFile));
        $this->instance = $newPath;
        return $this;
    }

    public function delete($pathFile)
    {
        if ($pathFile != null && $pathFile != '') {
            Storage::disk($this->disk)->delete(Str::after($pathFile, $this->folderPrefix));
        }
        return $this;
    }

    public function deleteSimpleFiles(array $files)
    {

        $files = array_map(function ($value) {
            $value = Str::after(Str::after($value, url('/')), 'public/uploads/');
            return $value;
        }, $files);

        $files = array_filter($files, function ($value) {
            return !Str::startsWith($value, 'files/');
        });

        Storage::disk($this->disk)->delete(array_values($files));
        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }


    /**
     * Upload a new avatar and replace the old one if it exists.
     *
     * @param string $folder Folder to store the avatar.
     * @param UploadedFile $newFile New avatar file.
     * @param string|null $currentAvatarPath Path to the current avatar to be replaced.
     * @return string New avatar path.
     */
    public function uploadAvatar(string $folder, UploadedFile $newFile, ?string $currentAvatarPath = null): string
    {
        // Set the storage folder
        $this->setFolder($folder);

        // Delete the existing avatar if it exists
        if ($currentAvatarPath) {
            $this->delete($currentAvatarPath);
        }

        // Upload the new file
        $path = $newFile->storeAs($this->folder, $newFile->hashName(), $this->disk);
        $this->instance = $this->folderPrefix . $path;

        return $this->instance;
    }

    public function uploadMultipleImages(string $folder, array $images): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            $uploadedPaths[] = $this->uploadAvatar($folder, $image);
        }

        return $uploadedPaths;
    }
}
