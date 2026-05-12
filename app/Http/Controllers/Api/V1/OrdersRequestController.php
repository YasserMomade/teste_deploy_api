<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrderRequestService;
use App\Traits\ApiResponse;
use App\Http\Requests\OrderRequest\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Models\Orders_request;


class OrdersRequestController extends Controller
{
     use ApiResponse;

    protected $orderRequestService;

    public function __construct(OrderRequestService $orderRequestService)
    {
        $this->orderRequestService = $orderRequestService;
    }

    public function index() : JsonResponse
    {
        try {
            $orderRequests = $this->orderRequestService->getAllOrders();
            return $this->success($orderRequests);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $orderRequest = $this->orderRequestService->createOrder($request->validated());
            return $this->created($orderRequest);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $orderRequest = $this->orderRequestService->getOrderById($id);
            if (!$orderRequest) {
                return $this->notFound();
            }
            return $this->success($orderRequest);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreOrderRequest $request, string $id): JsonResponse
    {
        try {
            $updatedOrderRequest = $this->orderRequestService->updateOrder(
                $id,
                $request->validated()
            );

            return $this->success($updatedOrderRequest);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function destroy(int $id): JsonResponse
    {
        try {
            $orderRequest = $this->orderRequestService->getOrderById($id);
            if (!$orderRequest) {
                return $this->notFound();
            }
            $this->orderRequestService->deleteOrder($orderRequest);
            return $this->success(null, 'orderRequest deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
