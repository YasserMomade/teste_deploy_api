<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\FIleService;

class FileController extends Controller
{
    use ApiResponse;

    protected $fileService;

    public function __construct(FIleService $fileService)
    {
        $this->fileService = $fileService;
    } 

     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $file = $this->fileService->getAllFiles();
            return $this->success($file);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $file = $request->file('file');

            $path = $file->store('orders/documents', 'public');

            $file = $this->fileService->createFile(
                [
                'document_type' => $request->document_type,
                'url' => $path,
                'order_id' => $request->order_id,
                'responsible_id' => auth()->id(),
                ]
            );
            return $this->created($file);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        try {
            $file = $this->fileService->getFileById($id);
            return $this->success($file);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Storefile $request, string $id)
    {
        try {
            $file = $this->fileService->updateFile($id, $request->validated());
            return $this->success($file);

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
