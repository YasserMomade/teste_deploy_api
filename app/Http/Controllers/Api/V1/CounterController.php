<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Counter\UpdateCounterRequest;
use App\Http\Requests\Country\StoreCounterRequest;
use App\Http\Resources\CounterResource;
use App\Models\Counter;
use App\Services\CounterService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CounterService $counterService) {}

    public function index(Request $request): JsonResponse
    {
        $counters = $this->counterService->list(
            $request->only(['search', 'country_id', 'per_page'])
        );

     return $this->success(
            CounterResource::collection($counters)
        );
    }

    public function store(StoreCounterRequest $request): JsonResponse
    {
        $counter = $this->counterService->create($request->validated());
        $counter->load('country');

        return $this->created(
            new CounterResource($counter), 'Counter created successfully.');
    }

    public function show(Counter $counter): JsonResponse
    {
        $counter->load('country');
        return $this->success(new CounterResource($counter));
    }

    public function update(UpdateCounterRequest $request, Counter $counter): JsonResponse
    {
        $updated = $this->counterService->update($counter, $request->validated());

        return $this->success(new CounterResource($updated), 'Counter updated successfully.');
    }

    public function destroy(Counter $counter): JsonResponse
    {
        try {
            $this->counterService->delete($counter);
            return $this->success(message: 'Counter deleted successfully.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}