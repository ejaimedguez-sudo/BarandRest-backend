<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CatalogMediaUploadController extends Controller
{
    private const MAX_DIMENSION = 1600;

    public function store(Request $request): JsonResponse
    {
        $requestId = (string) ($request->attributes->get('request_id') ?: $request->header('X-Request-Id') ?: '');
        if ($requestId === '') {
            $requestId = (string) \Illuminate\Support\Str::uuid();
        }
        $startedAt = microtime(true);

        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'image.required' => 'Debes seleccionar una imagen.',
            'image.file' => 'El archivo enviado no es valido.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'Formato no permitido. Usa JPG, PNG o WEBP.',
            'image.max' => 'El archivo excede el limite de 5 MB.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                code: 'validation_failed',
                message: 'No fue posible procesar la imagen enviada.',
                status: 422,
                requestId: $requestId,
                details: ['errors' => $validator->errors()->toArray()],
                startedAt: $startedAt,
                request: $request
            );
        }

        try {
            $data = $validator->validated();

            /** @var UploadedFile $file */
            $file = $data['image'];

            [$binary, $extension, $mime] = $this->compressImage($file);
            if ($binary === '') {
                return $this->errorResponse(
                    code: 'image_processing_failed',
                    message: 'No fue posible procesar la imagen.',
                    status: 422,
                    requestId: $requestId,
                    details: [],
                    startedAt: $startedAt,
                    request: $request
                );
            }

            $path = sprintf(
                'catalog-images/%s/%s.%s',
                now()->format('Y/m'),
                \Illuminate\Support\Str::uuid()->toString(),
                $extension
            );

            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            $stored = $disk->put($path, $binary, [
                'visibility' => 'public',
                'ContentType' => $mime,
            ]);

            if ($stored !== true) {
                return $this->errorResponse(
                    code: 'storage_write_failed',
                    message: 'No fue posible guardar la imagen.',
                    status: 500,
                    requestId: $requestId,
                    details: [],
                    startedAt: $startedAt,
                    request: $request
                );
            }

            [$width, $height] = $this->dimensionsFromBinary($binary);
            $originalSize = max(0, (int) ($file->getSize() ?? 0));
            $savedSize = strlen($binary);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $compressionRatio = $originalSize > 0
                ? round($savedSize / $originalSize, 4)
                : null;

            Log::info('catalog_media_upload.success', [
                'request_id' => $requestId,
                'path' => $path,
                'mime' => $mime,
                'saved_size' => $savedSize,
                'original_size' => $originalSize,
                'compression_ratio' => $compressionRatio,
                'width' => $width,
                'height' => $height,
                'duration_ms' => $durationMs,
                'role' => (string) $request->header('X-USER-ROLE', 'unknown'),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'ok' => true,
                'request_id' => $requestId,
                'url' => $disk->url($path),
                'path' => $path,
                'mime' => $mime,
                'size' => $savedSize,
                'width' => $width,
                'height' => $height,
                'checksum' => hash('sha256', $binary),
                'original_name' => $file->getClientOriginalName(),
                'original_size' => $originalSize,
                'compression_ratio' => $compressionRatio,
                'duration_ms' => $durationMs,
            ], 201, [
                'X-Request-Id' => $requestId,
            ]);
        } catch (Throwable $exception) {
            return $this->errorResponse(
                code: 'unexpected_error',
                message: 'Error interno al procesar la imagen.',
                status: 500,
                requestId: $requestId,
                details: [],
                startedAt: $startedAt,
                request: $request,
                exception: $exception
            );
        }
    }

    private function errorResponse(
        string $code,
        string $message,
        int $status,
        string $requestId,
        array $details,
        float $startedAt,
        Request $request,
        ?Throwable $exception = null
    ): JsonResponse {
        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

        $logLevel = $status >= 500 ? 'error' : 'warning';
        Log::log($logLevel, 'catalog_media_upload.failed', [
            'request_id' => $requestId,
            'code' => $code,
            'status' => $status,
            'duration_ms' => $durationMs,
            'role' => (string) $request->header('X-USER-ROLE', 'unknown'),
            'ip' => $request->ip(),
            'details' => $details,
            'exception' => $exception?->getMessage(),
        ]);

        return response()->json([
            'ok' => false,
            'request_id' => $requestId,
            'message' => $message,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
            'duration_ms' => $durationMs,
        ], $status, [
            'X-Request-Id' => $requestId,
        ]);
    }

    private function compressImage(UploadedFile $file): array
    {
        $inputPath = $file->getRealPath();
        $originalMime = strtolower((string) $file->getMimeType());
        $originalExtension = $this->extensionForMime($originalMime) ?: strtolower((string) $file->getClientOriginalExtension());

        $rawFromPathname = file_get_contents($file->getPathname());
        $fallbackBinary = $rawFromPathname !== false ? $rawFromPathname : '';

        if (! $inputPath || ! function_exists('imagecreatefromstring')) {
            return [
                $fallbackBinary,
                $originalExtension ?: 'jpg',
                $originalMime ?: 'image/jpeg',
            ];
        }

        $raw = file_get_contents($inputPath);
        if ($raw === false || $raw === '') {
            return [
                '',
                $originalExtension ?: 'jpg',
                $originalMime ?: 'image/jpeg',
            ];
        }

        $source = @imagecreatefromstring($raw);
        if (! $source) {
            return [$raw, $originalExtension ?: 'jpg', $originalMime ?: 'image/jpeg'];
        }

        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);
        $ratio = min(1, self::MAX_DIMENSION / max($srcWidth, $srcHeight));
        $targetWidth = max(1, (int) round($srcWidth * $ratio));
        $targetHeight = max(1, (int) round($srcHeight * $ratio));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        $targetMime = 'image/jpeg';
        $targetExtension = 'jpg';

        if (in_array($originalMime, ['image/png', 'image/webp'], true)) {
            if (function_exists('imagewebp')) {
                $targetMime = 'image/webp';
                $targetExtension = 'webp';
                imagepalettetotruecolor($source);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            } else {
                $targetMime = 'image/png';
                $targetExtension = 'png';
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);

        ob_start();
        if ($targetMime === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($canvas, null, 82);
        } elseif ($targetMime === 'image/png') {
            imagepng($canvas, null, 7);
        } else {
            imagejpeg($canvas, null, 82);
        }
        $compressed = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        if ($compressed === '') {
            return [$raw, $originalExtension ?: 'jpg', $originalMime ?: 'image/jpeg'];
        }

        return [$compressed, $targetExtension, $targetMime];
    }

    private function extensionForMime(string $mime): ?string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }

    private function dimensionsFromBinary(string $binary): array
    {
        if ($binary === '' || ! function_exists('getimagesizefromstring')) {
            return [null, null];
        }

        $info = @getimagesizefromstring($binary);
        if (! is_array($info)) {
            return [null, null];
        }

        $width = isset($info[0]) ? (int) $info[0] : null;
        $height = isset($info[1]) ? (int) $info[1] : null;

        return [$width, $height];
    }
}
