<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\FIleService;
use App\Http\Requests\File\StoreFile;

class FileController extends Controller
{
    use ApiResponse;

    protected $fileService;

    public function __construct(FIleService $fileService)
    {
        $this->fileService = $fileService;
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFile $request)
    {
        try {
            $file = $request->file('file');

            $path = $file->store('orders/documents', 'public');

            $file = $this->fileService->createFile(
                [
                'document_type' => $request->document_type,
                'url' => $path,
                'order_id' => $request->order_id,
                'responsible_id' => $request->responsible_id,
                ]
            );
            return $this->created($file);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
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
