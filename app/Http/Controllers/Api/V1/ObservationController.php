<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Observation\StoreObservationRequest;
use App\Http\Requests\Observation\UpdateObservationRequest;
use App\Http\Resources\ObservationResource;
use App\Models\Observation;
use App\Services\ObservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObservationController extends Controller
{

    use ApiResponse;

    public function __construct(private readonly ObservationService $observationService) {}

    public function index(int $orderId): JsonResponse
    {
        $observations = $this->observationService->listByOrder($orderId);

        return $this->success(ObservationResource::collection($observations));
    }

    public function store(StoreObservationRequest $request, int $orderId): JsonResponse
    {
        $observation = $this->observationService->create($orderId, $request->validated(), $request->user());

        return $this->created(new ObservationResource($observation), 'Observation created successfully.');
    }

    public function show(int $orderId, Observation $observation): JsonResponse
    {

        if ($observation->order_id !== $orderId) {
            return $this->notFound('Observation not found for this shipment.');
        }

        $observation->load('creator:id,name,user_code');

        return $this->success(new ObservationResource($observation));
    }

    public function update(UpdateObservationRequest $request, int $orderId, Observation $observation): JsonResponse
    {
        if ($observation->order_id !== $orderId) {
            return $this->notFound('Observation not found for this shipment.');
        }

        $updated = $this->observationService->update($observation, $request->validated());

        return $this->success(new ObservationResource($updated), 'Observation updated successfully.');
    }

    public function destroy(Request $request, int $orderId, Observation $observation): JsonResponse
    {
        if ($observation->order_id !== $orderId) {
            return $this->notFound('Observation not found for this shipment.');
        }

        try {
            $this->observationService->delete($observation, $request->user());
            return $this->success(message: 'Observation deleted successfully.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
}
