<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileManager
{
    /**
     * Upload a file to S3.
     *
     * @param \Illuminate\Http\Request|null $request
     * @param string|null $path
     * @param string|null $fileKey
     * @param int|null $height
     * @param int|null $width
     * @param bool $isUpdateFileName
     * @param bool $isPrivate
     * @param bool $isThumb
     * @return string|null
     */
    public static function upload($request = null, $path = null, $fileKey = null, $height = null, $width = null, $isUpdateFileName = true, $isPrivate = false, $isThumb = false)
    {
        if (!$request || !$request->hasFile($fileKey)) return null;

        $file = $request->file($fileKey);
        $extension = $file->getClientOriginalExtension() ?: 'doc';

        $fileName = $isUpdateFileName ? time() . rand(10000, 99999) . '.' . $extension : $fileKey . '.' . $extension;
        $projectPath = config('filesystems.disks.' . config('filesystems.default') . '.folder_path') . '/' . $path;
        \Log::info('File upload path', ['path' => $projectPath]);
        $storageDisk = config('filesystems.default');
        $visibility = $isPrivate ? null : 'public';

        // Resize if needed
        if ($height && $width && !$isThumb) {
            $resized = Image::make($file)->resize($height, $width)->encode($extension);
            Storage::disk($storageDisk)->put("{$projectPath}/{$fileName}", (string) $resized, $visibility);
        } else {
            Storage::disk($storageDisk)->put("{$projectPath}/{$fileName}", file_get_contents($file), $visibility);
        }

        // Create thumbnail
        // if ($isThumb) {
        //     $thumbHeight = $height ?: 150;
        //     $thumbWidth = $width ?: 150;
        //     $thumb = Image::make($file)->resize($thumbHeight, $thumbWidth)->encode($extension);
        //     Storage::disk($storageDisk)->put("{$projectPath}/thumb/{$fileName}", (string) $thumb, $visibility);
        // }

        return "{$path}/{$fileName}";
    }

    /**
     * Get full S3 file URL
     *
     * @param string|null $filePath
     * @return string|null
     */
    public static function getUrl($filePath = null)
    {
        if (!$filePath) return null;

        return sprintf(
            '%s/%s/%s',
            config('filesystems.disks.' . config('filesystems.default') . '.url'),
            config('filesystems.disks.' . config('filesystems.default') . '.folder_path'),
            $filePath
        );
    }

    /**
     * Delete file from S3.
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function delete($filePath = null)
    {
        if (!$filePath) return false;

        $projectPath = config('filesystems.disks.' . config('filesystems.default') . '.folder_path') . '/' . $filePath;

        return Storage::disk(config('filesystems.default'))->delete($projectPath);
    }
}
