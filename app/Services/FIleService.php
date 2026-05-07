<?php

namespace App\Services;

use App\Models\File;

class FileService {
    public function createFile(array $data)
    {
        return File::create($data);
    }

    public function getAllFiles()
    {
        return File::with('orders')->get();
    }

    public function getFileById(string $id)
    {
        return File::findOrFail($id);
    }

    public function updateFile(string $id, array $data)
    {
        $File = File::findOrFail($id);
        $File->update($data);

        return $File;
    }

    public function deleteFile(string $id)
    {
        $File = File::findOrFail($id);
        return $File->delete();
    }
}