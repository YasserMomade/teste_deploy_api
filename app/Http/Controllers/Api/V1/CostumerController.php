<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CostumerService;
use App\Services\OrderRequestService;
use App\Services\FileCostumerService;
use App\Traits\ApiResponse;
use App\Http\Requests\Costumer\StoreCostumer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class CostumerController extends Controller
{
    use ApiResponse;

    protected $costumerService;

    public function __construct(CostumerService $costumerService, OrderRequestService $orderRequestService)
    {
        $this->costumerService = $costumerService;
        $this->orderRequestService = $orderRequestService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $costumers = $this->costumerService->getAllCostumers($request);
            return $this->success($costumers);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreCostumer $request): JsonResponse
{
    try {
        $data = $request->validated();

        $costumer = DB::transaction(function () use ($data) {

            // 1. Criar cliente
            $costumer = $this->costumerService->createCostumer([
                'name'     => $data['name'],
                'lastname' => $data['lastname'],
                'phone'    => $data['phone'],
                'email'    => $data['email'],
            ]);

            foreach ($data['orders_request'] as $order_request) {
                $this->orderRequestService->createOrder([
                    'description'  => $order_request['description'],
                    'store_name'   => $order_request['store_name'],
                    'category'     => $order_request['category'],
                    'sub_category' => $order_request['sub_category'] ?? null,
                    'costumer_id'  => $costumer->id,
                ]);
            }

            return $costumer;
        });

        return $this->created(
            $costumer->load('orderRequest')
        );

    } catch (\Exception $e) {
        return $this->error($e->getMessage());
    }
}

    public function show(int $id): JsonResponse
    {
        try {
            $costumer = $this->costumerService->getCostumerById($id);
            if (!$costumer) {
                return $this->notFound();
            }
            return $this->success($costumer);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreCostumer $request, int $id): JsonResponse
    {
        try {
            $costumer = $this->costumerService->getCostumerById($id);
            if (!$costumer) {
                return $this->notFound();
            }
            $updatedcostumer = $this->costumerService->updateCostumer($costumer, $request->validated());
            return $this->success($updatedcostumer);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->costumerService->deleteCostumer($id);
            return $this->success(null, 'costumer deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
