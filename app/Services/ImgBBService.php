<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ImgBBService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.imgbb.key', '');
    }

    /**
     * Upload an image file to ImgBB and return the URL.
     */
    public function upload(UploadedFile $file): ?string
    {
        if (empty($this->apiKey)) {
            // Fallback: store locally if no API key configured
            return null;
        }

        $base64 = base64_encode(file_get_contents($file->getRealPath()));

        $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
            'key'   => $this->apiKey,
            'image' => $base64,
        ]);

        if ($response->successful() && $response->json('data.url')) {
            return $response->json('data.url');
        }

        return null;
    }
}
