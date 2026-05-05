<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use App\Http\Requests\Order\StoreOrder;
use App\Http\Controllers\Controller;



class OrderController extends Controller
{

use ApiResponse;
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }   

    public function index() : JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrders();
            return $this->success($orders);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreOrder $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return $this->created($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreOrder $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            $updatedOrder = $this->orderService->updateOrder($order, $request->validated());
            return $this->success($updatedOrder);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            $this->orderService->deleteOrder($order);
            return $this->success(null, 'Order deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
