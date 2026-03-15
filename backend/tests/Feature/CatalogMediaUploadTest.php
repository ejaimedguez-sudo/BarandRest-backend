<?php

namespace Tests\Feature;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_upload_catalog_images(): void
    {
        Storage::fake('public');

        $this->post('/api/catalog/media/upload', [
            'image' => UploadedFile::fake()->image('guest.jpg', 600, 400),
        ], [
            'X-USER-ROLE' => 'guest',
            'Accept' => 'application/json',
        ])->assertStatus(403);
    }

    public function test_gerente_can_upload_catalog_images(): void
    {
        Storage::fake('public');

        $response = $this->post('/api/catalog/media/upload', [
            'image' => UploadedFile::fake()->image('producto.png', 1200, 800),
        ], [
            'X-USER-ROLE' => 'gerente',
            'Accept' => 'application/json',
        ]);

        $response->assertCreated()
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('ok', true)
            ->assertJsonStructure(['ok', 'request_id', 'url', 'path', 'mime', 'size', 'width', 'height', 'checksum', 'original_name', 'original_size', 'compression_ratio', 'duration_ms']);

        $path = (string) $response->json('path');
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists($path);
        $this->assertStringStartsWith('/storage/catalog-images/', (string) $response->json('url'));
        $this->assertSame('producto.png', (string) $response->json('original_name'));
        $this->assertIsString($response->json('checksum'));
        $this->assertSame(64, strlen((string) $response->json('checksum')));
    }

    public function test_upload_validation_error_has_structured_payload(): void
    {
        Storage::fake('public');

        $response = $this->post('/api/catalog/media/upload', [
            'image' => UploadedFile::fake()->create('archivo.txt', 10, 'text/plain'),
        ], [
            'X-USER-ROLE' => 'gerente',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422)
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('ok', false)
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonStructure([
                'ok',
                'request_id',
                'message',
                'error' => [
                    'code',
                    'message',
                    'details' => ['errors'],
                ],
                'duration_ms',
            ]);
    }

    public function test_upload_reuses_incoming_request_id_header(): void
    {
        Storage::fake('public');

        $requestId = 'req-catalog-upload-123';

        $response = $this->post('/api/catalog/media/upload', [
            'image' => UploadedFile::fake()->image('producto.png', 800, 600),
        ], [
            'X-USER-ROLE' => 'gerente',
            'X-Request-Id' => $requestId,
            'Accept' => 'application/json',
        ]);

        $response->assertCreated()
            ->assertHeader('X-Request-Id', $requestId)
            ->assertJsonPath('request_id', $requestId);
    }

    public function test_missing_api_route_returns_structured_error_with_request_id(): void
    {
        $requestId = 'req-api-404-xyz';

        $response = $this->getJson('/api/route-not-found-for-test', [
            'X-Request-Id' => $requestId,
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(404)
            ->assertHeader('X-Request-Id', $requestId)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('request_id', $requestId)
            ->assertJsonPath('error.code', 'request_failed')
            ->assertJsonStructure([
                'ok',
                'request_id',
                'message',
                'error' => [
                    'code',
                    'message',
                    'details',
                ],
                'duration_ms',
            ]);
    }
}
