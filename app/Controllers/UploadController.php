<?php

namespace App\Controllers;

use App\Helpers\ImagesManager;
use App\Helpers\Response;

class UploadController
{
    public function __construct(private Response $response) {}

    public function uploadImageTemp(): string
    {
        $image = $_FILES['image'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$image) {
            return $this->response->json(['error' => 'Petición inválida'], 400);
        }

        $filename = ImagesManager::saveTemp($image);

        // Devolvemos el nombre generado
        return $this->response->json([
            'temp_filename' => $filename
        ]);
    }
}
