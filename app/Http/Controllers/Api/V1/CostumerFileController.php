<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FileCostumerService;
use App\Traits\ApiResponse;




class CostumerFileController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FileCostumerService $fileCostumerService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file'          => 'required|file|mimes:pdf|max:10240',
            'document_type' => 'required|string',
            'costumer_id'   => 'required|exists:costumers,id',
        ]);

        try {
            $path = $request->file('file')->store('costumers/documents', 'public');

            $file = $this->fileCostumerService->createFile([
                'document_type' => $request->document_type,
                'url'           => $path,
                'costumer_id'   => $request->costumer_id,
            ]);

            return $this->created($file);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}