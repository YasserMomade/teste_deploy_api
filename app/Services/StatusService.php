<?php

namespace App\Services;

use App\Models\Status;

class StatusService 
{
    public function createStatus(array $data)
    {
        return Status::create($data);
    }

    public function getAllStatus()
    {
        return Status::all();
    }

    public function getStatusById(string $id)
    {
        return Status::findOrFail($id);
    }

    public function updateStatus(string $id, array $data)
    {
        $status = Status::findOrFail($id);
        $status->update($data);

        return $status;
    }

    public function deleteStatus(string $id)
    {
        $status = Status::findOrFail($id);
        return $status->delete();
    }
}