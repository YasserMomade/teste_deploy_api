<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Services\StoreService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    use ApiResponse;

    public function __construct(private readonly StoreService $storeService) {}

    public function index(Request $request): JsonResponse
    {

        $stores = $this->storeService->list(
            $request->only(['search', 'per_page'])
        );

        return $this->success(
            StoreResource::collection($stores)
        );
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $store = $this->storeService->create($request->validated());

        return $this->created(
            new StoreResource($store),
            'Store created successfully.'
        );
    }

     public function show(Store $store): JsonResponse
    {
        return $this->success(new StoreResource($store));
    }

    public function update(UpdateStoreRequest $request, Store $store): JsonResponse
    {
        $updated = $this->storeService->update($store, $request->validated());
        return $this->success(new StoreResource($updated),  'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        $this->storeService->delete($store);
        return $this->success(message: 'Store deleted successfully,');
    }
}
