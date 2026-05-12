<?php

namespace App\Services;

use App\Models\File;

class FileService {
    public function createFile(array $data)
    {
        return File::create($data);
    }

    public function deleteFile(string $id)
    {
        $File = File::findOrFail($id);
        return $File->delete();
    }
}