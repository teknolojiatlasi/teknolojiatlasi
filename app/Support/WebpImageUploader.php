<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class WebpImageUploader
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const SCRIPT_EXTENSIONS = [
        'php',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'phtml',
        'phar',
        'cgi',
        'pl',
        'py',
        'sh',
        'exe',
        'js',
    ];

    public static function store(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 1920,
        int $maxHeight = 1920,
        int $quality = 82,
        string $errorKey = 'image',
    ): string {
        self::ensureFileIsAllowed($file, $errorKey);
        self::runAntivirusScan($file, $errorKey);

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return self::storeOriginalFile($file, $directory, $disk, $errorKey, 'Missing GD/WebP support');
        }

        $contents = $file->get();
        $imageInfo = @getimagesizefromstring($contents);

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                $errorKey => 'Gecerli bir resim dosyasi yukleyin.',
            ]);
        }

        self::ensureImageDimensionsAreSafe($imageInfo, $errorKey);

        $source = @imagecreatefromstring($contents);

        if (! $source) {
            return self::storeOriginalFile($file, $directory, $disk, $errorKey, 'imagecreatefromstring returned false');
        }

        [$sourceWidth, $sourceHeight] = $imageInfo;
        [$targetWidth, $targetHeight] = self::fitWithinBounds(
            $sourceWidth,
            $sourceHeight,
            $maxWidth,
            $maxHeight,
            $errorKey,
        );

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';

        ob_start();
        imagewebp($canvas, null, $quality);
        $binary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        if ($binary === '') {
            return self::storeOriginalFile($file, $directory, $disk, $errorKey, 'imagewebp produced empty output');
        }

        try {
            $stored = Storage::disk($disk)->put($path, $binary);
        } catch (\Throwable $exception) {
            self::logStorageFailure($disk, $directory, $path, $exception->getMessage());

            throw ValidationException::withMessages([
                $errorKey => 'Resim sunucuda kaydedilemedi.',
            ]);
        }

        if ($stored === false || ! Storage::disk($disk)->exists($path)) {
            self::logStorageFailure($disk, $directory, $path, 'Storage::put returned false or file does not exist after write.');

            throw ValidationException::withMessages([
                $errorKey => 'Resim sunucuda kaydedilemedi.',
            ]);
        }

        return $path;
    }

    private static function storeOriginalFile(
        UploadedFile $file,
        string $directory,
        string $disk,
        string $errorKey,
        string $reason,
    ): string {
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $path = trim($directory, '/') . '/' . Str::uuid() . '.' . $extension;

        try {
            $stream = fopen($file->getRealPath(), 'r');

            if ($stream === false) {
                throw new \RuntimeException('Unable to open uploaded file stream.');
            }

            $stored = Storage::disk($disk)->put($path, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $exception) {
            self::logStorageFailure($disk, $directory, $path, $exception->getMessage());

            throw ValidationException::withMessages([
                $errorKey => 'Resim sunucuda kaydedilemedi.',
            ]);
        }

        if ($stored === false || ! Storage::disk($disk)->exists($path)) {
            self::logStorageFailure($disk, $directory, $path, 'Fallback storage failed after WebP conversion issue.');

            throw ValidationException::withMessages([
                $errorKey => 'Resim sunucuda kaydedilemedi.',
            ]);
        }

        Log::warning('Image upload stored without WebP conversion', [
            'disk' => $disk,
            'directory' => $directory,
            'path' => $path,
            'reason' => $reason,
        ]);

        return $path;
    }

    private static function logStorageFailure(
        string $disk,
        string $directory,
        string $path,
        string $reason,
    ): void {
        $root = config("filesystems.disks.{$disk}.root");

        Log::error('Image upload storage failure', [
            'disk' => $disk,
            'directory' => $directory,
            'path' => $path,
            'root' => $root,
            'root_exists' => is_string($root) ? is_dir($root) : null,
            'root_writable' => is_string($root) ? is_writable($root) : null,
            'reason' => $reason,
        ]);
    }

    private static function ensureFileIsAllowed(UploadedFile $file, string $errorKey): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $maxBytes = max(1, (int) config('security.uploads.max_bytes', 4 * 1024 * 1024));

        if ($extension === '' || in_array($extension, self::SCRIPT_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                $errorKey => 'Riskli dosya uzantisi reddedildi.',
            ]);
        }

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw ValidationException::withMessages([
                $errorKey => 'Sadece JPG, JPEG, PNG veya WebP dosyalari yuklenebilir.',
            ]);
        }

        if ($file->getSize() > $maxBytes) {
            throw ValidationException::withMessages([
                $errorKey => 'Dosya boyutu izin verilen siniri asiyor.',
            ]);
        }

        $mimeType = $file->getMimeType();

        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                $errorKey => 'Desteklenmeyen veya riskli dosya tipi yuklendi.',
            ]);
        }
    }

    private static function fitWithinBounds(
        int $width,
        int $height,
        int $maxWidth,
        int $maxHeight,
        string $errorKey,
    ): array {
        if ($width <= 0 || $height <= 0) {
            throw ValidationException::withMessages([
                $errorKey => 'Resim boyutlari okunamadi.',
            ]);
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);

        return [
            max(1, (int) round($width * $ratio)),
            max(1, (int) round($height * $ratio)),
        ];
    }

    private static function ensureImageDimensionsAreSafe(array $imageInfo, string $errorKey): void
    {
        [$width, $height] = $imageInfo;

        $minWidth = max(1, (int) config('security.uploads.min_width', 1));
        $minHeight = max(1, (int) config('security.uploads.min_height', 1));
        $maxPixels = max(1, (int) config('security.uploads.max_pixels', 40_000_000));

        if ($width < $minWidth || $height < $minHeight) {
            throw ValidationException::withMessages([
                $errorKey => 'Resim boyutlari cok kucuk.',
            ]);
        }

        if (($width * $height) > $maxPixels) {
            throw ValidationException::withMessages([
                $errorKey => 'Resim boyutlari guvenlik sinirini asiyor.',
            ]);
        }
    }

    private static function runAntivirusScan(UploadedFile $file, string $errorKey): void
    {
        $commandTemplate = trim((string) config('security.uploads.antivirus_command', ''));

        if ($commandTemplate === '') {
            return;
        }

        $command = str_replace('{file}', $file->getRealPath(), $commandTemplate);
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(20);

        try {
            $process->run();
        } catch (\Throwable $exception) {
            Log::error('Antivirus scan execution failed', [
                'message' => $exception->getMessage(),
                'command' => $commandTemplate,
            ]);

            throw ValidationException::withMessages([
                $errorKey => 'Yuklenen dosya guvenlik taramasindan gecirilemedi.',
            ]);
        }

        if (! $process->isSuccessful()) {
            Log::warning('Antivirus scan rejected upload', [
                'command' => $commandTemplate,
                'exit_code' => $process->getExitCode(),
                'output' => trim($process->getOutput() . "\n" . $process->getErrorOutput()),
            ]);

            throw ValidationException::withMessages([
                $errorKey => 'Yuklenen dosya guvenlik taramasindan gecemedi.',
            ]);
        }
    }
}
