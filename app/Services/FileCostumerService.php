<?php

namespace App\Services;

use App\Models\FileCostumer;

class FileCostumerService {
    public function createFile(array $data)
    {
        return FileCostumer::create($data);
    }

    public function deleteFile(string $id)
    {
        $File = FileCostumer::findOrFail($id);
        return $File->delete();
    }
}