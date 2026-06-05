<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\FIleService;
use App\Http\Requests\File\StoreFile;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class FileController extends Controller
{
    use ApiResponse;

    protected $fileService;

    public function __construct(FIleService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(StoreFile $request)
    {
        try {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

            $uploadedFile = $request->file('file');

            $result = $cloudinary->uploadApi()->upload(
                $uploadedFile->getRealPath(),
                [
                    'folder' => 'portador_diario/orders',
                    'resource_type' => 'auto',
                ]
            );

            $file = $this->fileService->createFile([
                'document_type' => $request->document_type,
                'url' => $result['secure_url'],
                'order_id' => $request->order_id,
                'responsible_id' => $request->responsible_id,
            ]);

            return $this->created($file);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->fileService->deleteFile($id);
            return $this->success(null, 'file deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}